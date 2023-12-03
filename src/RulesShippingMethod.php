<?php

namespace PlebWooCommerceShippingRulesets;

use PlebWooCommerceShippingRulesets\Rule;

class RulesShippingMethod extends \WC_Shipping_Method
{
    public const METHOD_ID = 'pleb_rulesets_method';

    private $debug_mode = false;
    private $debug_infos = '';

    private $fee_cost           = '';
    private $cost               = '0';
    private $prices_include_tax = false;
    private $rulesets           = [];

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
        $this->method_description    = __('Set your own rulesets to calculate the shipping price', 'pleb');

        $this->supports              = [
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
                'desc_tip' => __("", 'pleb'),
            ],
            'cost'       => [
                'title'             => __('Base price', 'pleb'),
                'type'              => 'text',
                'placeholder'       => '',
                'description'       => __('Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce').'<br/><br/>'.__('Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'woocommerce'),
                'default'           => '0',
                'desc_tip'          => true,
                'sanitize_callback' => [$this, 'sanitize_cost'],
            ],
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
        $this->cost               = $this->get_option('cost', '0');
        $this->prices_include_tax = ($this->tax_status == 'none') ? false : ($this->get_option('prices_include_tax', 'no') === 'yes');



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
            $this->debug_infos .= '=> Fee '.$atts['percent'].'% = '.$this->fee_cost.'*'.(floatval($atts['percent']) / 100).' = '.$calculated_fee.'<br>';
        }

        if ($atts['min_fee'] && $calculated_fee < $atts['min_fee']) {
            $this->debug_infos .= '=> Fee '.$calculated_fee.' < min:'.$atts['min_fee'].' = '.$atts['min_fee'].'<br>';
            $calculated_fee = $atts['min_fee'];
        }

        if ($atts['max_fee'] && $calculated_fee > $atts['max_fee']) {
            $this->debug_infos .= '=> Fee '.$calculated_fee.' > max:'.$atts['max_fee'].' = '.$atts['max_fee'].'<br>';
            $calculated_fee = $atts['max_fee'];
        }

        return $calculated_fee;
    }

    protected function evaluate_cost($costrule, $package_qty, $package_cost)
    {
        $locale         = localeconv();
        $decimals       = [wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ','];

        // We place the current $packageprice to $this->fee_cost to use it in fee() function (temp shortcode)
        $this->fee_cost = $package_cost;

        $this->debug_infos .= '=> Tax: '.(($this->prices_include_tax) ? "inclusive" : "exclusive").'<br>';
        $this->debug_infos .= '=> Rule: '.$costrule.'<br>';

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
            $this->debug_infos .= '=> Qty: '.$package_qty.'<br>';
        }
        if(str_contains($costrule, '[cost]')) {
            $this->debug_infos .= '=> Cost: '.$this->fee_cost.'<br>';
        }

        // Clean up chars
        $sum = preg_replace('/\s+/', '', $sum);
        $sum = str_replace($decimals, '.', $sum);
        $sum = rtrim(ltrim($sum, "\t\n\r\0\x0B+*/"), "\t\n\r\0\x0B+-*/");

        $this->debug_infos .= '=> '.$sum.'<br>';

        include_once(WC()->plugin_path().'/includes/libraries/class-wc-eval-math.php');
        if(!$sum) {
            $sum = '0';
        }

        $result = \WC_Eval_Math::evaluate($sum);
        $this->debug_infos .= '=> '.$result.'<br>';
        return $result;
    }

    protected function get_package_item_qty($package)
    {
        $total_quantity = 0;
        foreach ($package['contents'] as $item_id => $values) {
            if ($values['quantity'] > 0 && $values['data']->needs_shipping()) {
                $total_quantity += $values['quantity'];
            }
        }
        return $total_quantity;
    }

    public function calculate_shipping($package = [])
    {
        $this->debug_infos = '';

        $rate = [
            'id'      => $this->get_rate_id(),
            'label'   => $this->title,
            'cost'    => 0,
            'package' => $package,
        ];

        //dump($package);

        $cost      = $this->get_option('cost', '0');
        if ('' !== $cost) {
            $package_cost = ($this->prices_include_tax) ? $package['cart_subtotal'] : $package['contents_cost'];
            $rate['cost'] = $this->evaluate_cost($cost, $this->get_package_item_qty($package), $package_cost);
        }

        // $shipping_classes = WC()->shipping()->get_shipping_classes();
        // dd($shipping_classes);

        $this->add_rate($rate);

        do_action('woocommerce_'.$this->id.'_shipping_add_rate', $this, $rate);

        if($this->debug_mode) {
            $wpPluginInstance = WordPressPlugin::instance();
            $notice_content = '<strong>'.$this->method_title.'</strong> [plugin:'.$wpPluginInstance->name.']<br>'.$this->debug_infos;
            wc_add_notice($notice_content, 'notice');
        }

    }

    public function generate_pleb_rulesets_html($key, $data)
    {
        $field_key = $this->get_field_key($key);

        $rulesets = $this->get_option($key);

        if(!is_array($rulesets)) {
            if (is_serialized($rulesets, false)) {
                $rulesets = @unserialize(trim($rulesets));
            }
        }
        if(!is_array($rulesets)) {
            $rulesets = [];
        }

        $rulesets = array_map(function ($ruleset) {
            return ($ruleset instanceof Ruleset) ? $ruleset : Ruleset::createFromArray($ruleset);
        }, $rulesets);

        // $ruleset1 = Ruleset::create();
        // $ruleset1->addRule(Rule::create());
        // $ruleset1->addRule(Rule::create());

        // $ruleset2 = Ruleset::create();
        // $ruleset2->addRule(Rule::create());

        // $rulesets = [
        //     $ruleset1,
        //     $ruleset2,
        // ];

        ob_start(); ?>
		<tr valign="top">
			<th scope="row" class="titledesc">
				<label><?php echo wp_kses_post($data['title']); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok.?></label>
			</th>
			<td class="forminp">
				<?php //dump($data);?>
                <?php //dump($rulesets);?>

                <?php if(empty($rulesets)): ?>
                    <div class="notice inline" style="margin-top:0;"><p><?php _e("No ruleset yet.", 'pleb'); ?></p></div>
                <?php else: ?>
                    <div class="metabox-holder">
                        <div id="pleb_rulesets" class="meta-box-sortables ui-sortable">
                            <?php foreach($rulesets as $ruleset): ?>
                                <?php echo $ruleset->htmlRender($field_key); ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <button type="button" class="button button-primary"><?php _e("Add new ruleset", 'pleb'); ?></button>
			</td>
		</tr>
		<?php
        return ob_get_clean();
    }

    public function validate_rulesets_field($key, $value)
    {
        if(!is_array($value)) {
            throw new \Exception(__("Invalid data format", 'pleb'));
        }

        return serialize($value);
    }

    public function sanitize_cost($value)
    {
        $value = is_null($value) ? '' : $value;
        $value = wp_kses_post(trim(wp_unslash($value)));
        $value = str_replace([get_woocommerce_currency_symbol(), html_entity_decode(get_woocommerce_currency_symbol())], '', $value);
        // Thrown an error on the front end if the evaluate_cost will fail.
        $dummy_cost = $this->evaluate_cost($value, 1, 1);
        if (false === $dummy_cost) {
            throw new \Exception(\WC_Eval_Math::$last_error);
        }
        return $value;
    }

}
