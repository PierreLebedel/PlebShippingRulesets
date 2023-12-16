<?php

/**
 * Plugin Name: WooCommerce Shipping Rulesets
 * Plugin URI: https://wordpress.org/plugins/pleb-woocommerce-shipping-rulesets/
 * Description: Make your own rulesets to calculate shipping rates
 * Version: 1.0.0
 * Author: Pierre Lebedel
 * Author URI: https://www.pierrelebedel.fr
 * Text Domain: pleb-woocommerce-shipping-rulesets
 * Domain Path: /languages
 * Requires at least: 6.4
 * Requires PHP: 8.0
 * WC requires at least: 8.3.0
 * WC tested up to: 8.4.0
 * Woo:
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') || exit;

spl_autoload_register(function ($class) {
	$prefix = 'PlebWooCommerceShippingRulesets\\';
	$base_dir = __DIR__.'/includes/';
	$len = strlen($prefix);
	if (strncmp($prefix, $class, $len) !== 0) {
		return;
	}
	$relative_class = substr($class, $len);
	$file = $base_dir.str_replace('\\', '/', $relative_class).'.php';
	if (file_exists($file)) {
		//echo "require: ".$file."<br>";
		require $file;
	} else {
		//echo "!!!require: ".$file."<br>";
	}
});

if (!function_exists('dump')) {
	function dump(...$args)
	{
		foreach ($args as $value) {
			echo '<pre style="display: block;color:#f0f0f1;background:#1d2327;border-radius:3px;margin:10px 0;padding:5px 10px;white-space:pre-wrap;overflow-x:auto;tab-width: 4;font-falimy:andale mono, monospace;">'.print_r($value, true).'</pre>';
		}
	}
}

if (!function_exists('dd')) {
	function dd(...$args)
	{
		dump(...$args);
		die();
	}
}

if (!class_exists('PlebWooCommerceShippingRulessets')) {
	class PlebWooCommerceShippingRulessets
	{
		public static function wordPressPluginInstance(): \PlebWooCommerceShippingRulesets\WordPressPlugin
		{
			return \PlebWooCommerceShippingRulesets\WordPressPlugin::instance(__FILE__);
		}

		public static function activate()
		{
		}

		public static function deactivate()
		{
		}
	}
}

register_activation_hook(__FILE__, [PlebWooCommerceShippingRulessets::class, 'activate']);
register_deactivation_hook(__FILE__, [PlebWooCommerceShippingRulessets::class, 'deactivate']);

add_action('plugins_loaded', [PlebWooCommerceShippingRulessets::class, 'wordPressPluginInstance'], 1);
