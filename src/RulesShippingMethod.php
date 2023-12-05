<?php

namespace PlebWooCommerceShippingRulesets;

use PlebWooCommerceShippingRulesets\Models\Rule;
use PlebWooCommerceShippingRulesets\Models\Ruleset;
use PlebWooCommerceShippingRulesets\Models\DefaultRuleset;
use WC_Product_Simple;

class RulesShippingMethod extends \WC_Shipping_Method
{
    public const METHOD_ID = 'pleb_rulesets_method';
    public $plugin_id = 'plebwcsr_';

    private $debug_mode = false;
    private $debug_infos = '';

    private $fee_cost = '';
    private $prices_include_tax = false;
    //private $always_enabled     = false;

    public static function autoRegister($methods)
    {
        $methods[self::METHOD_ID] = self::class;
        return $methods;
    }

    public function __construct(int $instance_id = 0)
    {
        $this->id                    = self::METHOD_ID;
        $this->instance_id           = absint($instance_id);
        $this->method_title          = __('Rulesets based shipping price', 'pleb');
        $this->method_description    = implode('<br>', [
            __('Set your own rulesets to calculate the shipping price on Cart & Checkout pages.', 'pleb'),
            __('The first ruleset whose rules all match the shopping cart will be applied to the order.', 'pleb'),
            __("This shipping method will not be available if the cart don't satisfies entirely none of rulesets.", 'pleb'),
            __("The default ruleset allows you to apply a rate even if none of rulesets matches the shopping cart.", 'pleb'),
        ]);

        $this->supports = [
            'settings',
            'shipping-zones',
            'instance-settings',
            //'instance-settings-modal',
        ];

        $this->form_fields = [
            'debug_mode' => [
                'title'       => __('Debug mode', 'pleb'),
                'type'        => 'checkbox',
                'label'       => __('Enable Debug Mode', 'pleb'),
            ],
        ];

        $this->instance_form_fields = [
            'title'      => [
                'title'       => __('Method title', 'woocommerce'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                'default'     => __('Rules based shipping price', 'pleb'),
                'desc_tip'    => true,
            ],
            'tax_status' => [
                'title'   => __('Tax status', 'woocommerce'),
                'type'    => 'select',
                'class'   => 'wc-enhanced-select',
                'default' => 'taxable',
                'options' => [
                    'taxable' => __('Taxable', 'woocommerce'),
                    'none'    => _x('None', 'Tax status', 'woocommerce'),
                ],
            ],
            'prices_include_tax' => [
                'title'    => __('Prices entered with tax', 'woocommerce'),
                'type'     => 'select',
                'default'  => 'no',
                'options'  => [
                    'yes' => __('Yes, I will enter prices inclusive of tax', 'woocommerce'),
                    'no'  => __('No, I will enter prices exclusive of tax', 'woocommerce'),
                ],
                'description' => __("", 'pleb'),
                'desc_tip' => __("", 'pleb'),
            ],
            'cost'       => [
                'title'             => __('Base price', 'pleb'),
                'type'              => 'text',
                'placeholder'       => '',
                'description'       => implode('<br>', [
                    sprintf(__("Enter a cost or sum, e.g. %s. Tags will be dynamically replaced in price calculation.", 'pleb'), '<code>10.00 * [qty]</code>'),
                    __("This base price will be added to the price of the group matching with the shopping cart.", 'pleb'),
                    sprintf(__("%s: Number of items in cart", 'pleb'), '<code>[qty]</code>'),
                    sprintf(__("%s: Shopping cart price", 'pleb'), '<code>[cost]</code>'),
                    sprintf(__("%s: Percentage based fees", 'pleb'), '<code>[fee percent="10" min_fee="20" max_fee=""]</code>'),
                ]),
                'default'           => '0',
                'desc_tip'          => __("Works the same as WooCommerce Flat Rate", 'pleb'),
                'sanitize_callback' => [$this, 'sanitize_cost'],
            ],
            // 'always_enabled' => array(
			// 	'title'       => __( 'Always enabled?', 'pleb' ),
			// 	'label'       => __( 'Enable this method even if none of rulesets matches the shopping cart', 'pleb' ),
			// 	'type'        => 'checkbox',
			// 	'description' => __("You can add a Default ruleset to apply custom price.", 'pleb' ),
			// 	'default'     => 'no',
			// 	'desc_tip'    => false,
			// ),
            'rulesets'       => [
                'title'             => __('Rulesets', 'pleb'),
                'type'              => 'pleb_rulesets',
                'placeholder'       => '',
                'description'       => __("", 'pleb'),
                'default'           => [],
                'desc_tip'          => true,
                //'sanitize_callback' => [$this, 'sanitize_cost'],
            ],
        ];

        $this->init();

        add_action('woocommerce_update_options_shipping_'.$this->id, [$this, 'process_admin_options']);
    }

    private function init()
    {
        $this->title              = $this->get_option('title');
        $this->tax_status         = $this->get_option('tax_status', 'taxable');
        $this->prices_include_tax = ($this->tax_status == 'none') ? false : ($this->get_option('prices_include_tax', 'no') === 'yes');
        //$this->always_enabled     = $this->get_option('always_enabled', 'no')!='no';

        $this->debug_mode = $this->get_option('debug_mode', 'no') === 'yes';
    }

    public function is_available($package)
    {
        return $this->is_enabled();
    }

    public function is_taxable()
    {
        if(!wc_tax_enabled()) {
            return false;
        }
        if($this->tax_status == 'none') {
            return false;
        }
        if(WC()->customer && WC()->customer->get_is_vat_exempt()) {
            return false;
        }
        if($this->prices_include_tax) {
            return false;
        }

        return true;
    }

    public function is_prices_include_tax(): bool
    {
        return $this->prices_include_tax;
    }

    public function fee($atts): float
    {
        $atts = shortcode_atts(
            [
                'percent' => '',
                'min_fee' => '',
                'max_fee' => '',
            ],
            $atts,
            'fee'
        );

        $calculated_fee = 0;

        if ($atts['percent']) {
            $calculated_fee = $this->fee_cost * (floatval($atts['percent']) / 100);
            $this->addDebugRow('Fee '.$atts['percent'].'% = '.$this->fee_cost.'*'.(floatval($atts['percent']) / 100).' = '.$calculated_fee);
        }

        if ($atts['min_fee'] && $calculated_fee < $atts['min_fee']) {
            $this->addDebugRow('Fee '.$calculated_fee.' < min:'.$atts['min_fee'].' = '.$atts['min_fee']);
            $calculated_fee = $atts['min_fee'];
        }

        if ($atts['max_fee'] && $calculated_fee > $atts['max_fee']) {
            $this->addDebugRow('Fee '.$calculated_fee.' > max:'.$atts['max_fee'].' = '.$atts['max_fee']);
            $calculated_fee = $atts['max_fee'];
        }

        return $calculated_fee;
    }

    public function cleanDebugRows(): self
    {
        $this->debug_infos = '';
        return $this;
    }

    public function addDebugRow($content): self
    {
        $this->debug_infos .= $content;
        $this->debug_infos .= '<br>';
        return $this;
    }

    protected function evaluate_cost(string $costrule = '', array $package = [], string $debugCostName = '')
    {
        if($costrule==='') return 0;

        $package_qty = 0;
        foreach ($package['contents'] as $item_id => $values) {
            if ($values['quantity'] > 0 && $values['data']->needs_shipping()) {
                $package_qty += $values['quantity'];
            }
        }

        // We place the current $packageprice to $this->fee_cost to use it in fee() function (temp shortcode)
        $this->fee_cost = ($this->is_prices_include_tax()) ? $package['cart_subtotal'] : $package['contents_cost'];

        $this->addDebugRow($debugCostName.'User input rule = '.$costrule);

        add_shortcode('fee', [$this, 'fee']);
        $sum = do_shortcode(
            str_replace([
                '[qty]',
                '[cost]',
            ], [
                $package_qty,
                $this->fee_cost,
            ], $costrule)
        );
        remove_shortcode('fee', [$this, 'fee']);

        if(str_contains($costrule, '[qty]')) {
            $this->addDebugRow($debugCostName.'Rule [qty] tag value = '.$package_qty);
        }
        if(str_contains($costrule, '[cost]')) {
            $this->addDebugRow($debugCostName.'Rule [cost] tag value = '.$this->fee_cost);
        }

        // Clean up chars
        $locale   = localeconv();
        $decimals = [wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ','];

        $sum = preg_replace('/\s+/', '', $sum);
        $sum = str_replace($decimals, '.', $sum);
        $sum = rtrim(ltrim($sum, "\t\n\r\0\x0B+*/"), "\t\n\r\0\x0B+-*/");

        $this->addDebugRow($debugCostName.'Values replaced rule = '.$sum);

        if(!$sum) $sum = '0';

        include_once(WC()->plugin_path().'/includes/libraries/class-wc-eval-math.php');
        $result = \WC_Eval_Math::evaluate($sum);
        $this->addDebugRow($debugCostName.'Math result rule = '.$result);

        return $result;
    }

    private function find_matching_ruleset(array $package = []): ?Ruleset
    {
        $rulesets = $this->get_classic_rulesets_array('rulesets');

        if(!empty($rulesets)){
            foreach($rulesets as $ruleset){

                if( $ruleset->matchToWooCommercePackageArray($package, $this->instance_id) ){
                    return $ruleset;
                }

            }
        }

        return $this->get_default_ruleset('rulesets');
    }

    private function get_dummy_woocommerce_package(): array
    {
        $product_1 = new WC_Product_Simple();
        $product_1->set_virtual(false);

        return [
            'cart_subtotal' => 10,
            'contents_cost' => 10,
            'contents' => [
                'item_1' => [
                    'quantity' => 1,
                    'data' => $product_1,
                ]
            ]
        ];
    }

    public function calculate_shipping($package = [])
    {
        $this->cleanDebugRows();

        $rate = [
            'id'      => $this->get_rate_id(),
            'label'   => $this->title,
            'cost'    => 0,
            'package' => $package,
        ];

        //dump($package);

        //$this->addDebugRow('Method always enabled = '.($this->always_enabled?'yes':'no'));
        $this->addDebugRow('Taxable = '.(($this->is_taxable()) ? "yes" : "no"));
        if($this->is_taxable()){
            $this->addDebugRow('Prices taxes = '.(($this->is_prices_include_tax()) ? "inclusive" : "exclusive"));
        }

        $basePrice = $this->get_option('cost', '0');
        if ( $basePrice !== '' ) {
            $rate['cost'] += $this->evaluate_cost($basePrice, $package, 'Base price cost: ');
        }

        $orderMatchingRuleset = $this->find_matching_ruleset($package);

        if($orderMatchingRuleset){
            $this->addDebugRow('Matching ruleset found : '.$orderMatchingRuleset->getName().($orderMatchingRuleset->isDefault()?' (default)':''));
            if($orderMatchingRuleset->getCost()!==''){
                $rate['cost'] += $this->evaluate_cost($orderMatchingRuleset->getCost(), $package, 'Ruleset cost: ');
            }
        }else{
            $this->addDebugRow('No matching ruleset found');
        }

        //if($orderMatchingRuleset || $this->always_enabled){
        if($orderMatchingRuleset){
            $this->add_rate($rate);
            do_action('woocommerce_'.$this->id.'_shipping_add_rate', $this, $rate);
        }

        if($this->debug_mode && (is_cart() || is_checkout()) ) {
            $wpPluginInstance = WordPressPlugin::instance();
            $notice_content = '<strong>'.$this->method_title.'</strong> [plugin:'.$wpPluginInstance->name.']<br>'.$this->debug_infos;
            wc_add_notice($notice_content, 'notice');
        }

    }

    private function get_rulesets_array(string $key = 'rulesets'): array
    {
        $rulesets = $this->get_option($key);

        if(!is_array($rulesets)) {
            if (is_serialized($rulesets, false)) {
                $rulesets = @unserialize(trim($rulesets));
            }
        }
        if(!is_array($rulesets)) {
            $rulesets = [];
        }

        return $rulesets;
    }

    private function get_classic_rulesets_array(string $key = 'rulesets'): array
    {
        $rulesets = $this->get_rulesets_array($key);

        $classicRulesets = [];

        foreach($rulesets as $ruleset){
            if( $ruleset instanceof DefaultRuleset ){
                continue;
            }elseif( $ruleset instanceof Ruleset ){
                $classicRulesets[] = $ruleset;
            }elseif(is_array($ruleset)){
                if(isset($ruleset['order']) && $ruleset['order']=='default'){
                    continue;
                }
                $classicRulesets[] = Ruleset::createFromArray($ruleset);
            }
        }

        return $classicRulesets;
    }

    public function get_default_ruleset(string $key = 'rulesets'): ?Ruleset
    {
        $rulesets = $this->get_rulesets_array($key);

        $defaultRuleset = null;

        foreach($rulesets as $ruleset){
            if( $ruleset instanceof DefaultRuleset ){
                $defaultRuleset = $ruleset;
                continue;
            }elseif( $ruleset instanceof Ruleset ){
                continue;
            }elseif(is_array($ruleset)){
                if(isset($ruleset['order']) && $ruleset['order']=='default'){
                    $defaultRuleset = DefaultRuleset::createFromArray($ruleset);
                }
            }
        }

        return $defaultRuleset;
    }

    /**
     * WooCommerce function to display "rulesets" field type
     * generate_pleb_{{ $type }}_html
     */
    public function generate_pleb_rulesets_html($key, $data)
    {
        $field_key = $this->get_field_key($key);

        $classicRulesets = $this->get_classic_rulesets_array($key);
        $defaultRuleset = $this->get_default_ruleset($key);

        ob_start(); ?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label><?php echo wp_kses_post($data['title']); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok.?></label>
			</th>
			<td class="forminp">
				<?php //dump($data);?>
                <?php //dump($rulesets);?>

                <?php /** Empty field required if no ruleset posted at all */ ?>
                <input type="hidden" name="<?php echo esc_attr($field_key); ?>" value="">

                <div id="pleb_no_ruleset_notice" class="notice notice-info inline text-center notice-alt" style="margin-top:0;margin-bottom:15px;<?php if(!empty($classicRulesets) || $defaultRuleset) {
                    echo 'display:none;';
                } ?>"><p><span class="dashicons dashicons-dismiss"></span> <?php _e("No ruleset yet.", 'pleb'); ?></p></div>

                <div class="metabox-holder" style="padding-top:0;">
                    <div id="pleb_rulesets" data-instance_id="<?php echo $this->instance_id; ?>" class="meta-box-sortables">
                        <?php if(!empty($classicRulesets)): ?>
                            <?php foreach($classicRulesets as $ruleset): ?>
                                <?php echo $ruleset->htmlRender($field_key); ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div id="pleb_no_ruleset_default_notice" class="notice notice-info inline text-center notice-alt" style="margin-top:0;margin-bottom:15px;<?php if($defaultRuleset) {
                        echo 'display:none;';
                    } ?>"><p><span class="dashicons dashicons-info"></span> <?php _e("No default ruleset yet.", 'pleb'); ?> <?php _e("The default ruleset allows you to apply a rate even if none of rulesets matches the shopping cart.", 'pleb'); ?></p></div>

                    <div id="pleb_ruleset_default_wrapper">
                        <?php if($defaultRuleset): ?>
                            <?php echo $defaultRuleset->htmlRender($field_key); ?>
                        <?php endif; ?>
                    </div>

                </div>

                <button id="pleb_ruleset_add_button" data-field_key="<?php echo $field_key; ?>" type="button" class="button"><?php _e("Add new ruleset", 'pleb'); ?></button>
                <button id="pleb_ruleset_add_default_button" data-field_key="<?php echo $field_key; ?>" type="button" class="button" style="<?php if($defaultRuleset){ echo 'display:none;'; } ?>"><?php _e("Add default ruleset", 'pleb'); ?></button>

			</td>
		</tr>
		<?php
        return ob_get_clean();
    }

    public function validate_rulesets_field($key, $value)
    {
        if(!is_array($value)) {
            $value = [];
        }

        return serialize($value);
    }

    public function sanitize_cost(string $value)
    {
        $value = is_null($value) ? '' : $value;
        $value = wp_kses_post(trim(wp_unslash($value)));
        $value = str_replace([get_woocommerce_currency_symbol(), html_entity_decode(get_woocommerce_currency_symbol())], '', $value);
        
        if( $value !== '' ){
            $dummy_cost = $this->evaluate_cost($value, $this->get_dummy_woocommerce_package(), 'Dummy cost: ');
            if (false === $dummy_cost) {
                throw new \Exception(\WC_Eval_Math::$last_error);
            }
        }

        return $value;
    }

}
