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
        require_once(ABSPATH.'wp-admin/includes/class-wp-filesystem-base.php');
        require_once(ABSPATH.'wp-admin/includes/class-wp-filesystem-direct.php');

        $fileSystemDirect = new WP_Filesystem_Direct(false);

        if (!WP_Filesystem()) {
            die('Unable to connect to thefilesystem');
        }

        echo "Make SVN directories".PHP_EOL;
        $pluginSvnDir = './svn';
        $pluginTrunkDir = $pluginSvnDir.'/trunk';
        $pluginAssetsDir = $pluginSvnDir.'/assets';

        if (!is_dir($pluginSvnDir)) {
            mkdir($pluginSvnDir, 0777, true);
        }

        if (is_dir($pluginTrunkDir)) {
            $fileSystemDirect->rmdir($pluginTrunkDir, true);
        }

        if (is_dir($pluginAssetsDir)) {
            $fileSystemDirect->rmdir($pluginAssetsDir, true);
        }

        mkdir($pluginTrunkDir, 0777, true);
        mkdir($pluginAssetsDir, 0777, true);

        echo " - Copy files in ".$pluginTrunkDir.PHP_EOL;
        copy_dir('./', $pluginTrunkDir, [
            'svn',
            'vendor',
            'docs',
            'includes/'.basename(__FILE__),
            '.gitignore',
            '.php-cs-fixer.cache',
            '.php-cs-fixer.php',
            'composer.json',
            'composer.lock',
            'LICENSE',
            'README.md',
            'assets',
            'loco.xml',
            '.git',
            'pleb-woocommerce-shipping-rulesets.zip',
        ]);

        echo " - Copy assets files in ".$pluginAssetsDir.PHP_EOL;
        copy_dir('./assets', $pluginAssetsDir, [
            'icon.svg', // not used in this plugin
            'icon.psd',
            'banner.psd',
        ]);

        /**
         *
         */

        $instance = self::instance();
        $plugin = $instance->getPlugin();

        $readmeContentArray = [
            "=== ".$plugin->getPluginData('Name')." ===",
            "Plugin URI: ".$plugin->getPluginData('PluginURI'),
            "Authors: ".$plugin->getPluginData('AuthorName'),
            "Author URI: ".$plugin->getPluginData('AuthorURI'),
            "Version: ".$plugin->getPluginData('Version'),
            "Last updated time: ".wp_date('Y-m-d'),
            "Creation time: 2023-12-11",
            "Contributors: pierre-lebedel",
            "Donate link: ",
            "Tags: woocommerce, shipping, rulesets, rates",
            "Requires at least: ".$plugin->getPluginData('RequiresWP'),
            "Tested up to: 6.4.2",
            "Stable tag: 1.0",
            "Requires PHP: ".$plugin->getPluginData('RequiresPHP'),
            "License: GPLv3",
            "License URI: https://www.gnu.org/licenses/gpl-3.0.html",
            "",
            $plugin->getPluginData('Description'),
        ];

        $readmeContentArray[] = "";
        $readmeContentArray[] = "== Purpose ==";
        $readmeContentArray[] = "";
        $readmeContentArray[] = "Once the plugin is installed and activated, you can create your own rulesets, consisting of the rules of your choice, by going to WooCommerce > Settings > Shipping, edit the shipping zone, and add Shipping Rulesets method.";
        $readmeContentArray[] = "Each of these rulesets is associated with a rate, which will be applied to the customer's shopping cart if it matches all the rules.";
        $readmeContentArray[] = "Like the original WooCommerce Flat Rate, the ruleset's rate is a formula that can include variables. Ex: €12.00 * [qty]";

        $readmeContentArray[] = "";
        $readmeContentArray[] = "== 3 usage modes ==";

        $readmeContentArray[] = "";
        $readmeContentArray[] = "= The first ruleset that match applies their rate =";
        $readmeContentArray[] = "";
        $readmeContentArray[] = "Rulesets should be ordered in order of priority. The ruleset with the highest priority that matches will apply its rate to the shopping cart.";

        $readmeContentArray[] = "";
        $readmeContentArray[] = "= Each of the rulesets that match applies its own rate =";
        $readmeContentArray[] = "";
        $readmeContentArray[] = "Rulesets should be ordered in display order. Each of the rulesets that match the shopping cart will be a separate rate, selectable by the customer.";

        $readmeContentArray[] = "";
        $readmeContentArray[] = "= All the rulesets that match are grouped together in one rate =";
        $readmeContentArray[] = "";
        $readmeContentArray[] = "Rulesets doesn't need to be ordered. Each of the rulesets that match the shopping cart will be added together in a single rate.";

        $faq = [
            "Can I override a rule to make my own WooCommerce cart comparaison?" => "Yes, you can use WordPress filters to add your own RuleCondition, that determines if the shopping cart allows this shipping method (and price). See docs for more infos.",
        ];

        if(!empty($faq)) {
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Frequently Asked Questions ==";

            foreach($faq as $q => $a) {
                $readmeContentArray[] = "";
                $readmeContentArray[] = "= ".$q." =";
                $readmeContentArray[] = "";
                $readmeContentArray[] = $a;
            }
        }

        $screenshots = [
			'1' => __("Add Shipping rulesets method to the shipping zone", 'pleb-woocommerce-shipping-rulesets'),
            '2' => __("Rulesets based shipping method display options form", 'pleb-woocommerce-shipping-rulesets'),
            '3' => __("Rulesets & rules settings metaboxes", 'pleb-woocommerce-shipping-rulesets'),
            '4' => __("Front end cart display shipping method name & rate", 'pleb-woocommerce-shipping-rulesets'),
            '5' => __("Front end debug mode enabled, shows matching ruleset(s)", 'pleb-woocommerce-shipping-rulesets'),
		];

        if(!empty($screenshots)) {
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Screenshots ==";
            $readmeContentArray[] = "";

            foreach($screenshots as $filename => $description) {
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
                    'Test 2',
                ],
                '' => [
                    "First relase",
                ],
            ],
        ];

        if(!empty($versionsChanges)) {
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Changelog ==";
            krsort($versionsChanges);
            foreach($versionsChanges as $version => $changesTypes) {
                $readmeContentArray[] = "";
                $readmeContentArray[] = "= ".$version." =";
                foreach($changesTypes as $changesType => $changes) {
                    foreach($changes as $change) {
                        $row = '';
                        if(empty($changesType)) {
                            $row .= "* ".$change.";";
                        } else {
                            $row .= "* ".$changesType.": ".$change.";";
                        }
                        $readmeContentArray[] = $row;
                    }
                }
            }
        }

        $upgradeNotices = [
            '1.0' => "Keep this plugin updated to access future rule types",
        ];

        if(!empty($upgradeNotices)) {
            $readmeContentArray[] = "";
            $readmeContentArray[] = "== Upgrade Notice ==";

            krsort($upgradeNotices);
            foreach($upgradeNotices as $version => $notice) {
                $readmeContentArray[] = "";
                $readmeContentArray[] = "= ".$version." =";
                $readmeContentArray[] = $notice;
            }

        }

        $readmeContent = implode(PHP_EOL, $readmeContentArray);

        echo " - Make ".$pluginTrunkDir.'/readme.txt'.PHP_EOL;
        file_put_contents($pluginTrunkDir.'/readme.txt', $readmeContent);

        /**
         *
         */

        echo "Create Zip file".PHP_EOL;
        $zip = new \ZipArchive();
        $zipFilePath = $plugin->slug.'.zip';

        if(file_exists($zipFilePath)) {
            unlink($zipFilePath);
        }

        if ($zip->open($zipFilePath, \ZipArchive::CREATE) != true) {
            die("Could not open archive");
        }

        $files = new \RecursiveIteratorIterator(
        	new \RecursiveDirectoryIterator(
            	$pluginTrunkDir,
            	\FilesystemIterator::SKIP_DOTS
            ),
        	\RecursiveIteratorIterator::LEAVES_ONLY
        );

        echo " - Zip plugin files: ";
        foreach ($files as $key => $file) {
            if ($file->getFilename() == '.' || $file->getFilename() == '..') {
                continue;
            }
            echo '.';
            $filePath = str_replace('\\', '/', $key);
            $filePath = str_replace('./svn/trunk/', '', $filePath);
            $zip->addFile($file->getRealPath(), $filePath);
        }
        echo PHP_EOL;

        $files = new \RecursiveIteratorIterator(
        	new \RecursiveDirectoryIterator(
            	$pluginAssetsDir,
            	\FilesystemIterator::SKIP_DOTS
            ),
        	\RecursiveIteratorIterator::LEAVES_ONLY
        );

        echo " - Zip assets files: ";
        foreach ($files as $key => $file) {
            if ($file->getFilename() == '.' || $file->getFilename() == '..') {
                continue;
            }
            echo '.';
            $filePath = str_replace('\\', '/', $key);
            $filePath = str_replace('./svn/', '', $filePath);
            $zip->addFile($file->getRealPath(), $filePath);
        }
        echo PHP_EOL;

        $zip->close();

    }

}
