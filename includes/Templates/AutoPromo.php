<?php

$pluginInstance = \PlebWooCommerceShippingRulesets\WordPressPlugin::instance();

?><br><div class="metabox-holder" style="width:100%;max-width:1005px;display:flex;justify-content:stretch;align-items:normal;gap:16px;">

    <div class="postbox" style="flex:auto;">
        <div class="postbox-header">
            <h2 class="hndle"><?php _e("Thank you tu use this plugin!", 'pleb-woocommerce-shipping-rulesets'); ?></h2>
        </div>
        <div class="inside">
            
        </div>
    </div>

    <div class="postbox" style="width:200px;">
        <div class="postbox-header">
            <h2 class="hndle"><?php _e("Useful links", 'pleb-woocommerce-shipping-rulesets'); ?></h2>
        </div>
        <div class="inside">
            <strong><?php _e("WordPress plugin", 'pleb-woocommerce-shipping-rulesets'); ?></strong>
            <ul>
                <li><a href="https://wordpress.org/plugins/<?php echo $pluginInstance->slug; ?>/" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Plugin directory", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
                <li><a href="https://translate.wordpress.org/projects/wp-plugins/<?php echo $pluginInstance->slug; ?>/" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Translating", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
            </ul>

            <strong><?php _e("Github files", 'pleb-woocommerce-shipping-rulesets'); ?></strong>
            <ul>
                <li><a href="<?php echo $pluginInstance->githubUri; ?>" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Github repository", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
                <li><a href="<?php echo trailingslashit($pluginInstance->githubUri); ?>issues" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Issues", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
                <li><a href="<?php echo trailingslashit($pluginInstance->githubUri); ?>tree/main/docs/index.md" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Docs", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
            </ul>

            <!-- <strong>Links</strong>
            <ul>
                <li>
                    <a href="#"><span class="dashicons dashicons-admin-links"></span>Internal</a>
                </li>
                <li>
                    <a href="#" target="_blank"><span class="dashicons dashicons-external"></span>External</a>
                </li>
            </ul> -->

        </div>
    </div>

</div>