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
    public $name;
    public $slug;
    public $dirUrl;
    public $baseName;
    public $version;
    public $textDomain;
    public $domainPath;
    public $pluginUri;

    public static function instance(?string $pluginMainFile = null): self
    {
        if(null === self::$instance) {
            self::$instance = new self($pluginMainFile);
        }
        return self::$instance;
    }

    private function __construct(?string $pluginMainFile = null)
    {
        $this->mainFile = $pluginMainFile;

        if(is_null($this->mainFile)) {
            $pluginPath = trailingslashit(dirname(__FILE__, 2));
            $pluginSlug = basename($pluginPath);
            $this->mainFile = $pluginPath.$pluginSlug.'.php';
        }

        $this->dirUrl = trailingslashit(plugin_dir_url($this->mainFile));
        $this->baseName = plugin_basename($this->mainFile);
        $this->slug = dirname($this->baseName);

        if(!function_exists('get_plugin_data')) {
            require_once(ABSPATH.'wp-admin/includes/plugin.php');
        }

        $pluginData = \get_plugin_data($this->mainFile);

        $this->name = $pluginData['Name'];
        $this->version = $pluginData['Version'];
        $this->textDomain = $pluginData['TextDomain'];
        $this->domainPath = trailingslashit($pluginData['DomainPath']);
        $this->pluginUri = $pluginData['PluginURI'];

        $this->loadPluginTextDomain();

        add_filter('plugin_row_meta', [$this, 'pluginRowMeta'], 10, 2);

        if(!class_exists('WooCommerce')) {
            $this->missingWooCommerceAdminNotice();
            return;
        }

        add_action('plugin_action_links_'.$this->baseName, [$this, 'pluginActionLinks']);
        add_filter('woocommerce_shipping_methods', [RulesShippingMethod::class, 'autoRegister']);
        add_action('admin_enqueue_scripts', [$this, 'loadAdminAssets']);
        add_action('before_woocommerce_init', [$this, 'setWooCommerceHposCompatibility']);

        $this->registerAjaxActions();
    }

    private function loadPluginTextDomain()
    {
        load_plugin_textdomain($this->textDomain, false, $this->slug.$this->domainPath);
    }

    private function registerAjaxActions()
    {
        AjaxAction::register('pleb_ruleset_template');
        AjaxAction::register('pleb_ruleset_rule_template');
    }

    private function missingWooCommerceAdminNotice()
    {
        // $message = sprintf(
        //     esc_html__('WooCommerce Shipping Rules requires WooCommerce to be installed and active. You can download %s here.', 'pleb'),
        //     '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>'
        // );

        $message = sprintf(
            esc_html__('%s requires WooCommerce to be installed and active. You can download %s here.', 'pleb'),
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

    public function loadAdminAssets()
    {
        $admin_script_handle = 'Pleb_WooCommerce_Shipping_Rulessets';

        wp_enqueue_script(
            $admin_script_handle,
            $this->dirUrl.'assets/admin-scripts.js',
            ['jquery-ui-sortable'],
            $this->version
        );

        wp_localize_script(
            $admin_script_handle,
            'pleb',
            [
                'plugin_version' => $this->version,
                'ajax_url' => admin_url('admin-ajax.php'),
            ]
        );
    }

    public function pluginActionLinks($links)
    {
        $action_links = [
            'settings' => '<a href="'.admin_url('admin.php?page=wc-settings&tab=shipping&section=pleb_rules_method').'">'.esc_html__('Settings', 'woocommerce').'</a>',
        ];

        return array_merge($action_links, $links);
    }

    public function pluginRowMeta($links, $file)
    {
        if ($this->baseName !== $file) {
            return $links;
        }

        $row_meta = [
            'docs' => '<a href="'.esc_url($this->pluginUri).'" target="_blank">'.esc_html__('Docs', 'pleb').'</a>',
        ];

        return array_merge($links, $row_meta);
    }

}
