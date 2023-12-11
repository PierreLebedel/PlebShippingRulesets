<?php

$pluginInstance = \PlebWooCommerceShippingRulesets\WordPressPlugin::instance();

?><br><div class="metabox-holder" style="width:100%;max-width:1005px;display:flex;justify-content:stretch;align-items:normal;gap:16px;">

    <div class="postbox" style="flex:auto;display:flex;flex-direction:column;">
        <div class="postbox-header">
            <h2 class="hndle"><?php _e("Thank you tu use this plugin!", 'pleb-woocommerce-shipping-rulesets'); ?></h2>
        </div>
        <div class="inside" style="padding-bottom: 0;flex:auto;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;">
            <div>
                <p style="margin:0 0 1em;">
                    <?php _e("Feel free to send your requests if you think certain essential rules are missing.", 'pleb-woocommerce-shipping-rulesets'); ?> 
                    <?php _e("I will work on these according on the time I have available.", 'pleb-woocommerce-shipping-rulesets'); ?>
                </p>
                <p style="margin:0 0;">
                    <?php echo sprintf(__("If you are a developer, feel free to contribute on %s!", 'pleb-woocommerce-shipping-rulesets'), '<a href="'.$pluginInstance->githubUri.'" target="_blank">Github</a>'); ?>
                </p>
            </div>
            
        </div>
    </div>

    <div class="postbox" style="display:flex;flex-direction:column;width:190px;min-width:190px;">
        <div class="postbox-header">
            <h2 class="hndle"><?php _e("WordPress plugin", 'pleb-woocommerce-shipping-rulesets'); ?></h2>
        </div>
        <div class="inside" style="padding-bottom: 0;flex:auto;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;">
            <ul>
                <li><a href="https://wordpress.org/plugins/<?php echo $pluginInstance->slug; ?>/" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Plugin directory", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
                <li><a href="https://wordpress.org/support/plugin/<?php echo $pluginInstance->slug; ?>/reviews/#new-post" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Add your review", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
                <li><a href="https://translate.wordpress.org/projects/wp-plugins/<?php echo $pluginInstance->slug; ?>/" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Translating", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
            </ul>
        </div>
    </div>

    <div class="postbox" style="display:flex;flex-direction:column;width:190px;min-width:190px;">
        <div class="postbox-header">
            <h2 class="hndle"><?php _e("Github files", 'pleb-woocommerce-shipping-rulesets'); ?></h2>
        </div>
        <div class="inside" style="padding-bottom: 0;flex:auto;display:flex;flex-direction:column;justify-content:center;align-items:flex-start;">
            <ul>
                <li><a href="<?php echo $pluginInstance->githubUri; ?>" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Github repository", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
                <li><a href="<?php echo trailingslashit($pluginInstance->githubUri); ?>issues" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Issues", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
                <li><a href="<?php echo trailingslashit($pluginInstance->githubUri); ?>tree/main/docs/index.md" target="_blank"><span class="dashicons dashicons-external"></span><?php _e("Docs", 'pleb-woocommerce-shipping-rulesets'); ?></a></li>
            </ul>
        </div>
    </div>

    <!-- <div class="postbox" style="width:200px;">
        <div class="postbox-header">
            <h2 class="hndle">Metabox title</h2>
        </div>
        <div class="inside" style="padding-bottom: 0;">
            <strong>Links</strong>
            <ul>
                <li>
                    <a href="#"><span class="dashicons dashicons-admin-links"></span>Internal</a>
                </li>
                <li>
                    <a href="#" target="_blank"><span class="dashicons dashicons-external"></span>External</a>
                </li>
            </ul>
        </div>
    </div> -->

</div>