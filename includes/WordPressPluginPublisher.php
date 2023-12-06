<?php

namespace PlebWooCommerceShippingRulesets;

use PlebWooCommerceShippingRulesets\WordPressPlugin;
use WP_Filesystem_Direct;

class WordPressPluginPublisher
{

    private static $instance = null;

    private $plugin;

    private static function instance(): self
	{
		if (null === self::$instance) {
            require_once(dirname(__FILE__, 5).'/wp-config.php');
            require_once(dirname(__FILE__, 5).'/wp-includes/functions.php');
            require_once(dirname(__FILE__, 5).'/wp-includes/formatting.php');
            require_once(dirname(__FILE__, 5).'/wp-includes/link-template.php');
            require_once(dirname(__FILE__, 5).'/wp-includes/plugin.php');

			self::$instance = new self();
            self::$instance->setPlugin(WordPressPlugin::instance());
		}

		return self::$instance;
	}

    private function setPlugin(WordPressPlugin $plugin)
    {
        $this->plugin = $plugin;
        return $this;
    }

    private function getPlugin(): WordPressPlugin
    {
        return $this->plugin;
    }

    public static function publish()
    {
        require_once(dirname(__FILE__, 5).'/wp-config.php');
        require_once(dirname(__FILE__, 5).'/wp-admin/includes/file.php');
        require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php' );
        require_once ( ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php' );

        $fileSystemDirect = new WP_Filesystem_Direct(false);

        if ( ! WP_Filesystem() ) {
            die('Unable to connect to thefilesystem');
        }

        $pluginDistDir = './wp-plugin';

        if (is_dir($pluginDistDir)) {
            $fileSystemDirect->rmdir($pluginDistDir, true);
        }

        mkdir($pluginDistDir, 0777, true);

        // copy_dir( './includes', $pluginDistDir.'/includes', [basename(__FILE__)] );
        // copy_dir( './languages', $pluginDistDir.'/languages' );
        // copy_dir( './admin', $pluginDistDir.'/admin' );


        copy_dir( './', $pluginDistDir.'', [
            'wp-plugin',
            'vendor',
            'docs',
            'includes/'.basename(__FILE__),
            '.gitignore',
            '.php-cs-fixer.cache',
            '.php-cs-fixer.php',
            'composer.json',
            'composer.lock',
        ] );

        /**
            === Plugin Name ===
            Plugin URI:
            Authors:
            Author URI:
            Version:
            Last updated time:
            Creation time
            Contributors: (this should be a list of wordpress.org userid's)
            Donate link: https://example.com/
            Tags: tag1, tag2
            Requires at least: 4.7
            Tested up to: 5.4
            Stable tag: 4.3
            Requires PHP: 7.0
            License: GPLv2 or later
            License URI: https://www.gnu.org/licenses/gpl-2.0.html
            Here is a short description of the plugin.  This should be no more than 150 characters.  No markup here.
         */

        $instance = self::instance();

        $plugin = $instance->getPlugin();

        //print_r($plugin);
    
        $readmeContentArray = [
            "=== ".$plugin->getPluginData('Name')." ===",
            "Plugin URI: ".$plugin->getPluginData('PluginURI'),
            "Authors: ".$plugin->getPluginData('AuthorName'),
            "Author URI: ".$plugin->getPluginData('AuthorURI'),
            "Version: ".$plugin->getPluginData('Version'),
            "Last updated time: ".date('Y-m-d'),
            "Creation time: 2023-12-06",
            "Contributors: pierre-lebedel",
            "Donate link: ",
            "Tags: woocommerce, shipping, rulesets, rates",
            "Requires at least: ".$plugin->getPluginData('RequiresWP'),
            "Tested up to: 6.4.1",
            "Stable tag: 0.1",
            "Requires PHP: ".$plugin->getPluginData('RequiresPHP'),
            "License: GPLv3",
            "License URI: https://www.gnu.org/licenses/gpl-3.0.html",
            "",
            $plugin->getPluginData('Description'),
        ];

        $faq = [
            "Can I override a rule to make my own WooCommerce cart comparaison?" => "Yes, you can use WordPress filters to add your own RuleCondition, that determines if the shopping cart allows this shipping method (and price). See docs for more infos."
        ];

        if(!empty($faq)){
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Frequently Asked Questions ==";

            foreach($faq as $q=>$a){
                $readmeContentArray[] = "";
                $readmeContentArray[] = "= ".$q." =";
                $readmeContentArray[] = "";
                $readmeContentArray[] = $a;
            }
        }

        $screenshots = [
			'1' => __("Screenshot #1 description", 'pleb'),
		];

        if(!empty($screenshots)){
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Screenshots ==";
            $readmeContentArray[] = "";
            
            foreach($screenshots as $filename => $description){
                $filenameWithoutDecoration = str_replace(['screenshot-', '.png', '.jpg'], [], $filename);
                $filenameNumber = intval($filenameWithoutDecoration);
                $readmeContentArray[] = $filenameNumber.". ".$description;
            }
        }

        $versionsChanges = [
            // '1.1 - 2023-12-07' => [
                // 'Added'    => [],
                // 'Updated'  => [],
                // 'Improved' => [],
                // 'Changed'  => [],
                // 'Fixed'    => [],
            // ],
            '1.0 - 2023-12-06' => [
                'Added'    => [],
                'Updated'  => [],
                'Improved' => [],
                'Changed'  => [],
                'Fixed'    => [
                    "Test 1",
                    'Test 2'
                ],
                '' => [
                    "First relase"
                ]
            ],
        ];

        if(!empty($versionsChanges)){
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Changelog ==";
            krsort($versionsChanges);
            foreach($versionsChanges as $version=>$changesTypes){
                $readmeContentArray[] = "";
                $readmeContentArray[] = "= ".$version." =";
                foreach($changesTypes as $changesType=>$changes){
                    foreach($changes as $change){
                        $row = '';
                        if(empty($changesType)){
                            $row .= "* ".$change.";";
                        }else{
                            $row .= "* ".$changesType.": ".$change.";";
                        }
                        $readmeContentArray[] = $row;
                    }
                }
            }
        }

        $upgradeNotices = [
            '1.0' => "Keep this plugin updated to access all new rule types",
        ];

        if(!empty($upgradeNotices)){
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Upgrade Notice ==";

            krsort($upgradeNotices);
            foreach($upgradeNotices as $version=>$notice){
                $readmeContentArray[] = "";
                $readmeContentArray[] = "= ".$version." =";
                $readmeContentArray[] = $notice;
            }
            
        }

        $readmeContent = implode(PHP_EOL, $readmeContentArray);

        file_put_contents($pluginDistDir.'/readme.txt', $readmeContent);

    }

    

    
}