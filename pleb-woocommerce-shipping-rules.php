<?php
/**
 * Plugin Name: WooCommerce Shipping Rules
 * Plugin URI:
 * Description: Make your own shipping rules
 * Version: 0.1.0
 * Author: Pierre Lebedel
 * Author URI: https://www.pierrelebedel.fr
 * Developer: Pierre Lebedel
 * Developer URI: https://www.pierrelebedel.fr
 * Text Domain: pleb
 * Domain Path: /languages
 *
 * Woo:
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

use PlebWooCommerceShippingRules\AdminNotice;
use PlebWooCommerceShippingRules\RulesShippingMethod;

defined('ABSPATH') || exit;

if (is_readable(__DIR__ . '/vendor/autoload.php')) {
    require_once(__DIR__ . '/vendor/autoload.php');
}

if (!class_exists('Pleb_WooCommerce_Shipping_Rules')) :
    class Pleb_WooCommerce_Shipping_Rules
    {
        private static $instance;

        private $version = '0.1.0';

        public function __construct()
        {
            //add_action('plugins_loaded', [$this, 'loadPluginTextDomain']);
            $this->loadPluginTextDomain();

            if(!class_exists('WooCommerce')) {
                $this->missingWooCommerceAdminNotice();
                return;
            }

            if(is_admin()) {
                add_action('admin_menu', [$this, 'addWooCommerceShippingSubMenuItem'], 100);
            }

            add_filter('woocommerce_shipping_methods', [RulesShippingMethod::class, 'autoRegister']);
        }

        public function __clone()
        {
            wc_doing_it_wrong(__FUNCTION__, __('Cloning is forbidden.', 'pleb'), $this->version);
        }

        public function __wakeup()
        {
            wc_doing_it_wrong(__FUNCTION__, __('Unserializing instances of this class is forbidden.', 'pleb'), $this->version);
        }

        public static function instance(): self
        {
            if(null === self::$instance) {
                self::$instance = new self();
            }
            return self::$instance;
        }

        public static function activate()
        {

        }

        public static function deactivate()
        {

        }

        public static function missingWooCommerceAdminNotice()
        {
            $message = sprintf(
                esc_html__('WooCommerce Shipping Rules requires WooCommerce to be installed and active. You can download %s here.', 'pleb'),
                '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>'
            );

            $notice = (new AdminNotice($message, 'error'))
                ->setStrong(true);

            add_action('admin_notices', $notice);
        }

        public function loadPluginTextDomain()
        {
            load_plugin_textdomain('pleb', false, plugin_basename(dirname(__FILE__)) . '/languages/');
        }

        public function addWooCommerceShippingSubMenuItem()
        {
            add_submenu_page(
                'edit.php?post_type=product',
                __('Product Grabber'),
                __('Grab New'),
                'manage_woocommerce', // Required user capability
                'ddg-product',
                'generate_grab_product_page'
            );
        }

    }
endif;

register_activation_hook(__FILE__, [Pleb_WooCommerce_Shipping_Rules::class, 'activate']);
register_deactivation_hook(__FILE__, [Pleb_WooCommerce_Shipping_Rules::class, 'deactivate']);

//Pleb_WooCommerce_Shipping_Rules::instance();
add_action('plugins_loaded', [Pleb_WooCommerce_Shipping_Rules::class, 'instance'], 1);
