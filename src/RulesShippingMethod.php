<?php

namespace PlebWooCommerceShippingRules;

class RulesShippingMethod extends \WC_Shipping_Method
{
    public const METHOD_ID = 'pleb_rules_method';

    public $fee_cost = '';
    public $cost = '';

    public static function autoRegister($methods)
    {
        $methods[self::METHOD_ID] = self::class;
        return $methods;
    }

    public function __construct(int $instance_id = 0)
    {
        $this->id                    = self::METHOD_ID;
        $this->instance_id           = absint($instance_id);
        $this->method_title          = __('Rules based shipping price', 'pleb');
        $this->method_description    = __('Set your own rulesets to calculate the shipping price', 'pleb');

        $this->supports              = [
            'settings',
            'shipping-zones',
            'instance-settings',
            'instance-settings-modal',
        ];

        // $this->form_fields = [
        //     'test'      => [
        //         'title'       => __('Method title', 'woocommerce'),
        //         'type'        => 'text',
        //         'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
        //         'default'     => __('Rules based shipping method', 'pleb'),
        //         'desc_tip'    => true,
        //     ],
        // ];

        $this->instance_form_fields = [
            'title'      => [
                'title'       => __('Method title', 'woocommerce'),
                'type'        => 'text',
                'description' => __('This controls the title which the user sees during checkout.', 'woocommerce'),
                'default'     => __('Rules based shipping method', 'pleb'),
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
            'cost'       => [
                'title'             => __('Base price', 'pleb'),
                'type'              => 'text',
                'placeholder'       => '',
                'description'       => __('Enter a cost (excl. tax) or sum, e.g. <code>10.00 * [qty]</code>.', 'woocommerce') . '<br/><br/>' . __('Use <code>[qty]</code> for the number of items, <br/><code>[cost]</code> for the total cost of items, and <code>[fee percent="10" min_fee="20" max_fee=""]</code> for percentage based fees.', 'woocommerce'),
                'default'           => '0',
                'desc_tip'          => true,
                'sanitize_callback' => [ $this, 'sanitize_cost' ],
            ],
            // 'min_amount'       => [
            //     'title'       => __('Minimum order amount', 'woocommerce'),
            //     'type'        => 'price',
            //     'placeholder' => wc_format_localized_price(0),
            //     'description' => __('Users will need to spend this amount to get free shipping (if enabled above).', 'woocommerce'),
            //     'default'     => '0',
            //     'desc_tip'    => true,
            // ],
        ];

        $this->init();

        add_action('woocommerce_update_options_shipping_' . $this->id, [ $this, 'process_admin_options' ]);
    }

    public function init()
    {
        $this->title      = $this->get_option('title');
        $this->tax_status = $this->get_option('tax_status');
        $this->cost       = $this->get_option('cost');
    }

    public function is_available($package)
    {
        return $this->is_enabled();
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
        }

        if ($atts['min_fee'] && $calculated_fee < $atts['min_fee']) {
            $calculated_fee = $atts['min_fee'];
        }

        if ($atts['max_fee'] && $calculated_fee > $atts['max_fee']) {
            $calculated_fee = $atts['max_fee'];
        }

        return $calculated_fee;
    }

    protected function evaluate_cost($costrule, $packageqty, $packageprice)
    {
        // Allow 3rd parties to process shipping cost arguments.
        $args           = apply_filters('woocommerce_evaluate_shipping_cost_args', ['qty' => $packageqty, 'cost' => $packageprice], $costrule, $this);
        $locale         = localeconv();
        $decimals       = [ wc_get_price_decimal_separator(), $locale['decimal_point'], $locale['mon_decimal_point'], ',' ];

        // We place the current $packageprice to $this->fee_cost to use it in fee() function (temp shortcode)
        $this->fee_cost = $packageprice;

        add_shortcode('fee', [$this, 'fee']);
        $sum = do_shortcode(
            str_replace([
                '[qty]',
                '[cost]',
            ], [
                $packageqty,
                $packageprice,
            ], $costrule)
        );
        remove_shortcode('fee', [$this, 'fee']);

        // Clean up chars
        $sum = preg_replace('/\s+/', '', $sum);
        $sum = str_replace($decimals, '.', $sum);
        $sum = rtrim(ltrim($sum, "\t\n\r\0\x0B+*/"), "\t\n\r\0\x0B+-*/");

        include_once(WC()->plugin_path() . '/includes/libraries/class-wc-eval-math.php');
        return $sum ? \WC_Eval_Math::evaluate($sum) : 0;
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
        $rate = [
            'id'      => $this->get_rate_id(),
            'label'   => $this->title,
            'cost'    => 0,
            'package' => $package,
        ];

        //dd($package);

        $cost      = $this->get_option('cost', '0');
        if ('' !== $cost) {
            $rate['cost'] = $this->evaluate_cost($cost, $this->get_package_item_qty($package), $package['contents_cost']);
        }


        // $shipping_classes = WC()->shipping()->get_shipping_classes();
        // dd($shipping_classes);

        $this->add_rate($rate);

        do_action('woocommerce_' . $this->id . '_shipping_add_rate', $this, $rate);
    }

}
