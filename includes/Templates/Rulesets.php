<tr valign="top">
    <th scope="row" class="titledesc">
        <label><?php echo wp_kses_post($data['title']); ?> <?php echo $this->get_tooltip_html($data); // WPCS: XSS ok.?></label>
    </th>
    <td class="forminp">
        <?php //dump($data);?>
        <?php //dump($rulesets);?>

        <?php /** Empty field required if no ruleset posted at all */ ?>
        <input type="hidden" name="<?php echo esc_attr($field_key); ?>" value="">

        <div id="pleb_no_ruleset_notice" class="notice notice-info inline text-center notice-alt" style="margin-top:0;margin-bottom:15px;<?php if (!empty($classicRulesets) || $defaultRuleset) {
            echo 'display:none;';
        } ?>"><p><span class="dashicons dashicons-dismiss"></span> <?php _e("No ruleset yet.", 'pleb'); ?></p></div>

        <div class="metabox-holder" style="padding-top:0;">
            <div id="pleb_rulesets" data-instance_id="<?php echo $this->instance_id; ?>" class="meta-box-sortables">
                <?php if (!empty($classicRulesets)) : ?>
                    <?php foreach ($classicRulesets as $ruleset) : ?>
                        <?php echo $ruleset->htmlRender($field_key); ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div id="pleb_no_ruleset_default_notice" class="notice notice-info inline text-center notice-alt" style="margin-top:0;margin-bottom:15px;<?php if ($defaultRuleset) {
                echo 'display:none;';
            } ?>"><p><span class="dashicons dashicons-info"></span> <?php _e("No default ruleset yet.", 'pleb'); ?> <?php _e("The default ruleset allows you to apply a rate even if none of rulesets matches the shopping cart.", 'pleb'); ?></p></div>

            <div id="pleb_ruleset_default_wrapper">
                <?php if ($defaultRuleset) : ?>
                    <?php echo $defaultRuleset->htmlRender($field_key); ?>
                <?php endif; ?>
            </div>

        </div>

        <button id="pleb_ruleset_add_button" data-field_key="<?php echo $field_key; ?>" type="button" class="button"><?php _e("Add new ruleset", 'pleb'); ?></button>
        <button id="pleb_ruleset_add_default_button" data-field_key="<?php echo $field_key; ?>" type="button" class="button" style="<?php if ($defaultRuleset) {
            echo 'display:none;';
        } ?>"><?php _e("Add default ruleset", 'pleb'); ?></button>

    </td>
</tr>