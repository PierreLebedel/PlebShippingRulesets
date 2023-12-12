<?php

namespace PlebWooCommerceShippingRulesets;

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

	public static function autoRegister($methods)
	{
		$methods[self::METHOD_ID] = self::class;
		return $methods;
	}

	public function __construct(int $instance_id = 0)
	{
		$this->id                    = self::METHOD_ID;
		$this->instance_id           = absint($instance_id);
		$this->method_title          = __('Shipping rulesets', 'pleb-woocommerce-shipping-rulesets');
		$this->method_description    = implode('<br>', [
			__("Set your own rulesets to calculate the shipping rate on Cart & Checkout pages.", 'pleb-woocommerce-shipping-rulesets'),
			__("This shipping method will not be available if the cart don't satisfies entirely none of rulesets.", 'pleb-woocommerce-shipping-rulesets'),
			__("The default ruleset allows you to apply a rate even if none of rulesets matches the shopping cart.", 'pleb-woocommerce-shipping-rulesets'),
		]);

		$this->supports = [
			'settings',
			'shipping-zones',
			'instance-settings',
			//'instance-settings-modal',
		];

		$this->form_fields = [
			// [
			// 	'type'  => 'pleb_tabs',
			// 	'default' => 'tab1',
			// 	'tabs' => [
			// 		'tab1' => [
			// 			'title' => __('Settings', 'pleb-woocommerce-shipping-rulesets'),
			// 			'content' => "Test settings",
			// 		],
			// 		'tab2' => [
			// 			'title' => __('Docs', 'pleb-woocommerce-shipping-rulesets'),
			// 			'content' => "Test docs",
			// 		],
			// 	],
			// ],
			[
				'type'  => 'pleb_autopromo',
			],
			[
				'type'  => 'title',
				'title' => __('Global settings', 'pleb-woocommerce-shipping-rulesets'),
			],
			'debug_mode' => [
				'title'       => __('Debug mode', 'pleb-woocommerce-shipping-rulesets'),
				'type'        => 'checkbox',
				'label'       => __('Enable Debug Mode', 'pleb-woocommerce-shipping-rulesets'),
			],
		];

		$this->instance_form_fields = [
			'title'      => [
				'title'       => __('Method title', 'woocommerce'),
				'type'        => 'text',
				'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
				'default'     => __('Rules based shipping price', 'pleb-woocommerce-shipping-rulesets'),
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
				'description' => __("", 'pleb-woocommerce-shipping-rulesets'),
				'desc_tip' => __("", 'pleb-woocommerce-shipping-rulesets'),
			],
			'rulesets_matching_mode' => [
				'title'    => __('Shipping rate(s) displayed', 'pleb-woocommerce-shipping-rulesets'),
				'type'     => 'select',
				'default'  => 'first',
				'options'  => [
					'first' => __("Single shipping rate based on the first ruleset with all rules matching the shopping cart", 'pleb-woocommerce-shipping-rulesets'),
					'many_grouped'  => __("Single shipping rate suming costs of all rulesets with all rules matching the shopping cart", 'pleb-woocommerce-shipping-rulesets'),
					'many_distinct' => __("Each matching ruleset is available as distinct shipping rate", 'pleb-woocommerce-shipping-rulesets'),
				],
				'description' => __("", 'pleb-woocommerce-shipping-rulesets'),
				'desc_tip' => __("", 'pleb-woocommerce-shipping-rulesets'),
			],
			'replace_method_title' => [
				'title'       => __('Replace method title?', 'pleb-woocommerce-shipping-rulesets'),
				'label'       => __('Replace method title by matching ruleset name in the shopping cart?', 'pleb-woocommerce-shipping-rulesets'),
				'type'        => 'checkbox',
				'description' => __("", 'pleb-woocommerce-shipping-rulesets'),
				'default'     => 'no',
				'desc_tip'    => false,
			],
			'cost'       => [
				'title'             => __('Base price', 'pleb-woocommerce-shipping-rulesets'),
				'type'              => 'text',
				'placeholder'       => '',
				'description'       => implode('<br>', [
					sprintf(__("Enter a cost or sum, e.g. %s. Tags will be dynamically replaced in price calculation.", 'pleb-woocommerce-shipping-rulesets'), '<code>10.00 * [qty]</code>'),
					__("This base price will be added to the price of the group matching with the shopping cart.", 'pleb-woocommerce-shipping-rulesets'),
					sprintf(__("%s: Number of items in cart", 'pleb-woocommerce-shipping-rulesets'), '<code>[qty]</code>'),
					sprintf(__("%s: Shopping cart price", 'pleb-woocommerce-shipping-rulesets'), '<code>[cost]</code>'),
					sprintf(__("%s: Percentage based fees", 'pleb-woocommerce-shipping-rulesets'), '<code>[fee percent="10" min_fee="20" max_fee=""]</code>'),
				]),
				'default'           => '0',
				'desc_tip'          => __("Works the same as WooCommerce Flat Rate", 'pleb-woocommerce-shipping-rulesets'),
				'sanitize_callback' => [$this, 'sanitize_cost'],
			],
			'cost_min_max'       => [
				'title'             => __('Price limits', 'pleb-woocommerce-shipping-rulesets'),
				'type'              => 'pleb_minmax',
				'default'           => [
					'min' => '', 
					'max' => ''
				],
				'description'       => implode('<br>', [
					__("These fields will limit the prices dynamically calculated by the rulesets.", 'pleb-woocommerce-shipping-rulesets'),
					__("They can also contain variables.", 'pleb-woocommerce-shipping-rulesets').' '.sprintf(
						__("For example, you can set shipping costs of %s minimum, and %s of the order price maximum, using %s %s and %s %s", 'pleb-woocommerce-shipping-rulesets'), 
						wc_price(5.5), 
						'10%',
						__("Min:",'pleb-woocommerce-shipping-rulesets'), 
						'<code>5.5</code>', 
						__("Max:",'pleb-woocommerce-shipping-rulesets'), 
						'<code>[cost] * 0.1</code>'),
				]),
				'desc_tip'          => sprintf(
					__("Works the same as %s setting field", 'pleb-woocommerce-shipping-rulesets'),
					'<b>'.__('Base price', 'pleb-woocommerce-shipping-rulesets').'</b>'
				),
				'sanitize_callback' => [$this, 'sanitize_cost_min_max'],
			],
			'rulesets'       => [
				'title'             => __('Rulesets', 'pleb-woocommerce-shipping-rulesets'),
				'type'              => 'pleb_rulesets',
				'placeholder'       => '',
				'description'       => __("", 'pleb-woocommerce-shipping-rulesets'),
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

		$this->debug_mode = $this->get_option('debug_mode', 'no') === 'yes';
	}

	public function is_available($package)
	{
		return $this->is_enabled();
	}

	public function is_taxable()
	{
		if (!wc_tax_enabled()) {
			return false;
		}
		if ($this->tax_status == 'none') {
			return false;
		}
		if (WC()->customer && WC()->customer->get_is_vat_exempt()) {
			return false;
		}
		if ($this->do_prices_include_tax()) {
			return false;
		}

		return true;
	}

	public function do_prices_include_tax(): bool
	{
		return ($this->tax_status == 'none') ? false : ($this->get_option('prices_include_tax', 'no') === 'yes');
	}

	public function do_replace_method_title(): bool
	{
		return ($this->get_option('replace_method_title', 'no') === 'yes');
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

	protected function evaluate_cost(string $costrule = '', array $package = [], string $debugCostName = '', array $replaces = [])
	{
		if ($costrule === '') {
			return 0;
		}

		$package_qty = 0;
		foreach ($package['contents'] as $item_id => $values) {
			if ($values['quantity'] > 0 && $values['data']->needs_shipping()) {
				$package_qty += $values['quantity'];
			}
		}

		// We place the current $packageprice to $this->fee_cost to use it in fee() function (temp shortcode)
		$this->fee_cost = ($this->do_prices_include_tax()) ? $package['cart_subtotal'] : $package['contents_cost'];

		$this->addDebugRow($debugCostName.'User input rule = '.$costrule);

		$replaces = array_merge([
			'[qty]' => $package_qty,
			'[cost]' => $this->fee_cost,
		], $replaces);

		add_shortcode('fee', [$this, 'fee']);
		$sum = do_shortcode(strtr($costrule, $replaces));
		remove_shortcode('fee', [$this, 'fee']);

		foreach($replaces as $k=>$v){
			if (str_contains($costrule, $k)) {
				$this->addDebugRow($debugCostName.'Rule '.$k.' tag value = '.$v);
			}
		}

		// Clean up chars
		$locale   = localeconv();
		$decimals = [wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ','];

		$sum = preg_replace('/\s+/', '', $sum);
		$sum = str_replace($decimals, '.', $sum);
		$sum = rtrim(ltrim($sum, "\t\n\r\0\x0B+*/"), "\t\n\r\0\x0B+-*/");

		$this->addDebugRow($debugCostName.'Values replaced rule = '.$sum);

		if (!$sum) {
			$sum = '0';
		}

		include_once(WC()->plugin_path().'/includes/libraries/class-wc-eval-math.php');
		set_error_handler(function() use ($debugCostName, $sum) {
			//$this->addDebugRow($debugCostName.'Formula error : '.$sum);
		});
		$result = \WC_Eval_Math::evaluate($sum);
		restore_error_handler(); 
			
		$this->addDebugRow($debugCostName.'Math result rule = '.$result);

		return $result;
	}

	private function find_matching_rulesets(array $package = []): array
	{
		$matchingRulesets = [];

		$getOnlyFirst = ($this->get_option('rulesets_matching_mode', 'first') === 'first');

		// Adds the non-default ruleset matching all the rules
		$classicRulesets = $this->get_classic_rulesets_array('rulesets');
		if (!empty($classicRulesets)) {
			foreach ($classicRulesets as $ruleset) {
				if ($ruleset->matchToWooCommercePackageArray($package, $this->instance_id)) {
					$matchingRulesets[$ruleset->getId()] = $ruleset;

					if($getOnlyFirst){
						return $matchingRulesets;
					}
				}
			}
		}

		$defaultRuleset = $this->get_default_ruleset('rulesets');
		if($defaultRuleset){
			$matchingRulesets[$defaultRuleset->getId()] = $defaultRuleset;

			if($getOnlyFirst){
				return $matchingRulesets;
			}
		}

		return $matchingRulesets;
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
				],
			],
		];
	}

	public function calculate_shipping($package = [])
	{
		//dump($package);
		$this->cleanDebugRows();

		$rate = [
			'id'      => $this->get_rate_id(),
			'label'   => $this->title,
			'cost'    => 0,
			'package' => $package,
		];

		$this->addDebugRow('Taxable = '.(($this->is_taxable()) ? "yes" : "no"));
		if ($this->is_taxable()) {
			$this->addDebugRow('Prices taxes = '.(($this->do_prices_include_tax()) ? "inclusive" : "exclusive"));
		}

		$rateId =  $this->get_rate_id();
		$rateLabel = $this->title;
		$baseCost = $rulesetsCost = 0;

		$basePrice = $this->get_option('cost', '0');
		if ($basePrice !== '') {
			$baseCost = $this->evaluate_cost($basePrice, $package, 'Base price cost: ');
		}

		$min = $max = null;
		$minMax = $this->get_option('cost_min_max');

		if ($minMax['min'] !== '') {
			$min = $this->evaluate_cost($minMax['min'], $package, 'Min price: ');
		}
		if ($minMax['max'] !== '') {
			$maxTemp = $this->evaluate_cost($minMax['max'], $package, 'Max price: ');
			$max = ($min) ? max($min, $maxTemp) : $maxTemp;
		}

		$orderMatchingRulesets = $this->find_matching_rulesets($package);

		if (!empty($orderMatchingRulesets)) {

			$matchingMode = $this->get_option('rulesets_matching_mode', 'first');

			foreach($orderMatchingRulesets as $matchingRuleset){
				$this->addDebugRow('Matching ruleset found : '.$matchingRuleset->getName().($matchingRuleset->isDefault() ? ' (default)' : ''));

				if($matchingMode!='many_grouped' && $this->do_replace_method_title()){
					$rateLabel = $matchingRuleset->getName();
				}

				if ($matchingRuleset->getCost() !== '') {
					$replaces = [];
					$rules = $matchingRuleset->getRules();
					if(!empty($rules)){
						foreach($rules as $rule){
							$condition = $rule->getCondition();
							if(!$condition) continue; 
                			if(!method_exists($condition, 'extractValueFromWooCommercePackageArray')) continue;
							$replaces[ '[rule_'.$rule->getId().']' ] = $condition->extractValueFromWooCommercePackageArray($package, $rule, $this->instance_id) ?? '0';
						}
					}

					$rulesetDebugTitle = ($matchingRuleset->isDefault()) ? 'Ruleset cost (default): ' : 'Ruleset cost: ';
					$rulesetsCost += $this->evaluate_cost($matchingRuleset->getCost(), $package, $rulesetDebugTitle, $replaces);
				}

				if($matchingMode == 'many_distinct'){
					$rateCost = ($baseCost + $rulesetsCost);

					if( $min && $rateCost < $min ){
						$rateCost = $min;
					}
					if( $max && $rateCost > $max ){
						$rateCost = $max;
					}

					$this->add_rate([
						'id'      => $this->get_rate_id($matchingRuleset->getId()),
						'label'   => $rateLabel,
						'cost'    => $rateCost,
						'package' => $package,
					]);
					do_action('woocommerce_'.$this->id.'_shipping_add_rate', $this, $rate);
					$rulesetsCost = 0; // reset to zero for the next loop
				}

			}
			
		} else {
			$this->addDebugRow('No matching ruleset found');
		}

		if (!empty($orderMatchingRulesets) && $matchingMode != 'many_distinct') {
			$rateCost = ($baseCost + $rulesetsCost);

			if( $min && $rateCost < $min ){
				$rateCost = $min;
			}
			if( $max && $rateCost > $max ){
				$rateCost = $max;
			}

			$this->add_rate([
				'id'      => $rateId,
				'label'   => $rateLabel,
				'cost'    => $rateCost,
				'package' => $package,
			]);
			do_action('woocommerce_'.$this->id.'_shipping_add_rate', $this, $rate);
		}

		if ($this->debug_mode && (is_cart() || is_checkout())) {
			$wpPluginInstance = WordPressPlugin::instance();
			$notice_content = '<strong>'.$this->method_title.'</strong> [plugin:'.$wpPluginInstance->name.']<br>'.$this->debug_infos;
			wc_add_notice($notice_content, 'notice');
		}
	}

	private function get_rulesets_array(string $key = 'rulesets'): array
	{
		$rulesets = $this->get_option($key);

		if (!is_array($rulesets)) {
			if (is_serialized($rulesets, false)) {
				$rulesets = @unserialize(trim($rulesets));
			}
		}
		if (!is_array($rulesets)) {
			$rulesets = [];
		}

		return $rulesets;
	}

	private function get_classic_rulesets_array(string $key = 'rulesets'): array
	{
		$rulesets = $this->get_rulesets_array($key);

		$classicRulesets = [];

		foreach ($rulesets as $ruleset) {
			if ($ruleset instanceof DefaultRuleset) {
				continue;
			} elseif ($ruleset instanceof Ruleset) {
				$classicRulesets[] = $ruleset;
			} elseif (is_array($ruleset)) {
				if (isset($ruleset['order']) && $ruleset['order'] == 'default') {
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

		foreach ($rulesets as $ruleset) {
			if ($ruleset instanceof DefaultRuleset) {
				$defaultRuleset = $ruleset;
				continue;
			} elseif ($ruleset instanceof Ruleset) {
				continue;
			} elseif (is_array($ruleset)) {
				if (isset($ruleset['order']) && $ruleset['order'] == 'default') {
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

		ob_start();
		include(dirname(__FILE__).'/Templates/Rulesets.php');
		return ob_get_clean();
	}

	public function validate_rulesets_field($key, $value)
	{
		if (!is_array($value)) {
			$value = [];
		}
		return serialize($value);
	}

	public function generate_pleb_tabs_html( $key, $data ) {
		$fieldKey = $this->get_field_key( $key );
		$data = wp_parse_args( $data, [
			'default'=>'',
			'tabs' => [
				'tab1' => [
					'title' => 'Title',
					'content' => 'Content',
				]
			],
		] );

		if(!array_key_exists($data['default'], $data['tabs'])){
			$data['default'] = array_key_first($data['tabs']);
		}

		ob_start();
		?></table>
		
		<div class="pleb_nav_tabs">

			<h2 class="nav-tab-wrapper">
				<?php foreach($data['tabs'] as $k=>$tab): ?>
				<a href="#<?php echo $fieldKey; ?>_<?php echo $k; ?>" class="nav-tab <?php if($data['default']==$k): ?>nav-tab-active<?php endif; ?>"><?php echo $tab['title']; ?></a>
				<?php endforeach; ?>
			</h2>
			
			<?php foreach($data['tabs'] as $k=>$tab): ?>
			<div class="tab_content" id="<?php echo $fieldKey; ?>_<?php echo $k; ?>" style="<?php if($data['default']!=$k): ?>display:none;<?php endif; ?>">
				<?php dump($tab['content']); ?>
			</div>
			<?php endforeach; ?>

		</div>

		<table class="form-table">
		<?php

		return ob_get_clean();
	}

	public function generate_pleb_autopromo_html( $key, $data ) {
		$fieldKey = $this->get_field_key($key);
		$data = wp_parse_args( $data, [
			'default' => __("Default", 'pleb-woocommerce-shipping-rulesets')
		] );

		ob_start();
		include(dirname(__FILE__).'/Templates/AutoPromo.php');
		return ob_get_clean();
	}

	public function sanitize_cost(string $value)
	{
		$value = is_null($value) ? '' : $value;
		$value = wp_kses_post(trim(wp_unslash($value)));
		$value = str_replace([get_woocommerce_currency_symbol(), html_entity_decode(get_woocommerce_currency_symbol())], '', $value);

		if ($value !== '') {
			$dummy_cost = $this->evaluate_cost($value, $this->get_dummy_woocommerce_package(), 'Dummy cost: ');
			if (false === $dummy_cost) {
				throw new \Exception(\WC_Eval_Math::$last_error);
			}
		}

		return $value;
	}

	public function generate_pleb_minmax_html( $key, $data ) {
		$fieldKey = $this->get_field_key($key);
		$defaults  = array(
			'title'             => 'Min / Max',
			'default'           => [
				'min' => '', 
				'max' => '2'
			],
			'desc_tip'          => false,
			'description'       => '',
		);

		$data = wp_parse_args( $data, $defaults );
		$values = $this->get_option( $key );

		ob_start();
		?><tr valign="top">
			<th scope="row" class="titledesc">
				<label><?php echo wp_kses_post( $data['title'] ); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok.?></label>
			</th>
			<td class="forminp">
				<div style="width:400px;">
					<div style="float:left;width:48%;">
						<label for="<?php echo esc_attr( $fieldKey ); ?>_min" style="display:block;margin-bottom:4px;">
							<?php _e("Min:", 'pleb-woocommerce-shipping-rulesets'); ?>
						</label>
						<input type="text" name="<?php echo esc_attr( $fieldKey ); ?>[min]" id="<?php echo esc_attr( $fieldKey ); ?>_min" value="<?php echo esc_attr( wc_format_localized_price($values['min']) ); ?>" style="width:100%;margin:0;" />
					</div>
					<div style="float:right;width:48%;">
						<label for="<?php echo esc_attr( $fieldKey ); ?>_max" style="display:block;margin-bottom:4px;">
							<?php _e("Max:", 'pleb-woocommerce-shipping-rulesets'); ?>
						</label>
						<input type="text" name="<?php echo esc_attr( $fieldKey ); ?>[max]" id="<?php echo esc_attr( $fieldKey ); ?>_max" value="<?php echo esc_attr( wc_format_localized_price($values['max']) ); ?>" style="width:100%;margin:0;" />
					</div>
					<div style="clear:both;"></div>
				</div>
				<?php echo $this->get_description_html( $data ); // WPCS: XSS ok. ?>
			</td>
		</tr><?php
		return ob_get_clean();
	}

	public function sanitize_cost_min_max(array $value)
	{
		return array_merge([
			'min' => '',
			'max' => '',
		], $value);
	}

}
