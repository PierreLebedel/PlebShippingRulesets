<?php

namespace PlebWooCommerceShippingRulesets;

use PlebWooCommerceShippingRulesets\AjaxAction;
use PlebWooCommerceShippingRulesets\AdminNotice;
use Automattic\WooCommerce\Utilities\FeaturesUtil;
use PlebWooCommerceShippingRulesets\RulesShippingMethod;

class WordPressPlugin
{
	private static $instance = null;

	public $mainFile;
	public $pluginData = null;

	public $name;
	public $slug;
	public $dirUrl;
	public $baseName;
	public $version;
	public $textDomain;
	public $domainPath;
	public $pluginUri;
	public $githubUri = 'https://github.com/PierreLebedel/WooCommerceShippingRulesets/';

	public static function instance(?string $pluginMainFile = null): self
	{
		if (null === self::$instance) {
			self::$instance = new self($pluginMainFile);
		}
		return self::$instance;
	}

	private function __construct(?string $pluginMainFile = null)
	{
		$this->mainFile = $pluginMainFile;

		if (is_null($this->mainFile)) {
			$pluginPath = trailingslashit(dirname(__FILE__, 2));
			$pluginSlug = basename($pluginPath);
			$this->mainFile = $pluginPath.$pluginSlug.'.php';
		}

		$this->dirUrl = trailingslashit(plugin_dir_url($this->mainFile));
		$this->baseName = plugin_basename($this->mainFile);
		$this->slug = dirname($this->baseName);

		$this->name       = $this->getPluginData('Name');
		$this->version    = $this->getPluginData('Version');
		$this->textDomain = $this->getPluginData('TextDomain');
		$this->domainPath = trailingslashit($this->getPluginData('DomainPath'));
		$this->pluginUri  = $this->getPluginData('PluginURI');

		$this->loadPluginTextDomain();

		add_filter('plugin_row_meta', [$this, 'pluginRowMeta'], 10, 2);

		if (!class_exists('WooCommerce')) {
			$this->missingWooCommerceAdminNotice();
			return;
		}

		add_action('plugin_action_links_'.$this->baseName, [$this, 'pluginActionLinks']);
		add_filter('woocommerce_shipping_methods', [RulesShippingMethod::class, 'autoRegister']);
		add_action('admin_enqueue_scripts', [$this, 'loadAdminJs']);
		add_action('admin_print_styles', [$this, 'loadAdminCss'], 11);
		add_action('before_woocommerce_init', [$this, 'setWooCommerceHposCompatibility']);

		$this->registerAjaxActions();
	}

	public function getPluginData(string $dataName, string $default = '')
	{
		if(is_null($this->pluginData)) {
			if (!function_exists('get_plugin_data')) {
				require_once(ABSPATH.'wp-admin/includes/plugin.php');
			}
			$this->pluginData = \get_plugin_data($this->mainFile, false, false);
		}
		if(array_key_exists($dataName, $this->pluginData)) {
			return $this->pluginData[$dataName];
		}
		return $default;
	}

	private function loadPluginTextDomain()
	{
		load_plugin_textdomain($this->textDomain, false, $this->slug.$this->domainPath);
	}

	private function registerAjaxActions()
	{
		AjaxAction::register('pleb_ruleset_template');
		AjaxAction::register('pleb_ruleset_default_template');
		AjaxAction::register('pleb_ruleset_rule_template');
		//AjaxAction::register('pleb_ruleset_delete');
		//AjaxAction::register('pleb_rule_delete');
		AjaxAction::register('pleb_rule_template');
		AjaxAction::register('pleb_ruleset_generate_id');
	}

	private function missingWooCommerceAdminNotice()
	{
		// $message = sprintf(
		//     esc_html__('WooCommerce Shipping Rules requires WooCommerce to be installed and active. You can download %s here.', 'pleb-woocommerce-shipping-rulesets'),
		//     '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>'
		// );

		$message = sprintf(
			esc_html__('%s requires WooCommerce to be installed and active. You can download %s here.', 'pleb-woocommerce-shipping-rulesets'),
			$this->name,
			'<a href="'.admin_url('plugin-install.php?s=WooCommerce&tab=search&type=term').'">WooCommerce</a>'
		);

		$notice = (new AdminNotice($message, 'error'))
			->setStrong(false);

		add_action('admin_notices', $notice);
	}

	public function setWooCommerceHposCompatibility()
	{
		if (class_exists(FeaturesUtil::class)) {
			FeaturesUtil::declare_compatibility('custom_order_tables', $this->mainFile, true);
		}
	}

	public function loadAdminJs()
	{
		if( get_current_screen()->id != 'woocommerce_page_wc-settings' ) return;
		
		$admin_script_handle = 'pleb_wcsr';

		wp_enqueue_script(
			$admin_script_handle,
			$this->dirUrl.'admin/js/plebwcsr.js',
			['jquery-ui-sortable'],
			$this->version
		);

		wp_localize_script(
			$admin_script_handle,
			'plebjs',
			[
				'plugin_version' => $this->version,
				'ajax_url' => admin_url('admin-ajax.php'),
				'shipping_method' => [
					'plugin_id' => 'plebwcsr_',
					'method_id' => 'pleb_rulesets_method',
				],
				'translations' => [
					'loading' => __("Loading...", 'pleb-woocommerce-shipping-rulesets'),
				],
			]
		);
	}

	public function loadAdminCss()
	{
		if( get_current_screen()->id != 'woocommerce_page_wc-settings' ) return;

		$admin_css_handle = 'pleb_wcsr';

		wp_enqueue_style(
			$admin_css_handle, 
			$this->dirUrl.'admin/css/plebwcsr.css'
		);
	}

	public function pluginActionLinks($links)
	{
		$action_links = [
			'settings' => '<a href="'.admin_url('admin.php?page=wc-settings&tab=shipping&section='.RulesShippingMethod::METHOD_ID).'">'.esc_html__('Settings', 'woocommerce').'</a>',
		];

		return array_merge($action_links, $links);
	}

	public function pluginRowMeta($links, $file)
	{
		if ($this->baseName !== $file) {
			return $links;
		}

		$row_meta = [
			'docs' => '<a href="'.trailingslashit($this->githubUri).'tree/main/docs/index.md" target="_blank">'.esc_html__('Docs', 'pleb-woocommerce-shipping-rulesets').'</a>',
		];

		return array_merge($links, $row_meta);
	}

}
