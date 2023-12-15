<tr valign="top">
    <th scope="row" class="titledesc">
        <label><?php echo wp_kses_post($data['title']); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok.?></label>
    </th>
    <td class="forminp">
        <?php //dump($data);?>
        <?php //dump($rulesets);?>

        <div style="max-width:900px;">

            <?php /** Empty field required if no ruleset posted at all */ ?>
            <input type="hidden" name="<?php echo esc_attr($field_key); ?>" value="">

            <div id="pleb_no_ruleset_notice" class="notice notice-info inline notice-alt pleb_notice" style="<?php if (!empty($classicRulesets) || $defaultRuleset): ?>display:none;<?php endif; ?>"><p><span class="dashicons dashicons-dismiss"></span> <?php _e("No ruleset yet.", 'pleb-woocommerce-shipping-rulesets'); ?></p></div>

            <div class="metabox-holder" style="padding-top:0;">
                <div id="pleb_rulesets" data-instance_id="<?php echo $this->instance_id; ?>" class="meta-box-sortables">
                    <?php if (!empty($classicRulesets)) : ?>
                        <?php foreach ($classicRulesets as $ruleset) : ?>
                            <?php echo $ruleset->htmlRender($field_key); ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div id="pleb_no_ruleset_default_notice" class="notice notice-info inline notice-alt pleb_notice" style="<?php if ($defaultRuleset): ?>display:none;<?php endif; ?>"><p><span class="dashicons dashicons-info"></span> <?php _e("No default ruleset yet.", 'pleb-woocommerce-shipping-rulesets'); ?> <?php _e("The default ruleset allows you to apply a rate even if none of rulesets matches the shopping cart.", 'pleb-woocommerce-shipping-rulesets'); ?></p></div>

                <div id="pleb_ruleset_default_wrapper">
                    <?php if ($defaultRuleset) : ?>
                        <?php echo $defaultRuleset->htmlRender($field_key); ?>
                    <?php endif; ?>
                </div>

            </div>

            <button id="pleb_ruleset_add_button" data-field_key="<?php echo $field_key; ?>" type="button" class="button"><span class="dashicons dashicons-plus-alt pleb_icon"></span><?php _e("Add new ruleset", 'pleb-woocommerce-shipping-rulesets'); ?></button>
            <button id="pleb_ruleset_add_default_button" data-field_key="<?php echo $field_key; ?>" type="button" class="button" style="<?php if ($defaultRuleset): ?>display:none;<?php endif; ?>"><span class="dashicons dashicons-yes-alt pleb_icon"></span><?php _e("Add default ruleset", 'pleb-woocommerce-shipping-rulesets'); ?></button>

        </div>

    </td>
</tr>