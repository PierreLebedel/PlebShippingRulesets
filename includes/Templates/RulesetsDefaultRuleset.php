<div class="postbox pleb_ruleset">

    <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
    <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="default">

    <div class="postbox-header pleb_title_input_wrapper" style="padding:4px 0;">
        <h2 class="hndle" style="cursor:auto;justify-content:flex-start;">
            <span class="text"><?php echo $this->getName(); ?></span>&nbsp;
            <em style="opacity:0.6;">(<?php _e("Default", 'pleb-shipping-rulesets'); ?>)</em>
        </h2>
        
        <div class="pleb_input_wrapper" style="display:none;height:30px;padding:3px 12px;">
            <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" placeholder="<?php esc_attr_e("Ruleset name", 'pleb-shipping-rulesets'); ?>" required>
        </div>
        
        <div class="handle-actions" style="padding-right:12px;">
            <button class="button pleb_edit_ruleset_button">
                <span class="button_dynamic_action"><span class="dashicons dashicons-edit pleb_icon"></span><?php _e("Edit", 'pleb-shipping-rulesets'); ?></span>
                <span class="button_dynamic_action" style="display:none;"><?php _e("Stop editing", 'pleb-shipping-rulesets'); ?></span>
            </button>

            <div style="float:right;padding-top:7px;padding-left:5px;">
                <a href="#" class="pleb_linkdanger pleb_ruleset_default_delete" data-confirm="<?php esc_attr_e("Are you sure to delete default ruleset?", 'pleb-shipping-rulesets'); ?>" title="<?php esc_attr_e("Delete", 'pleb-shipping-rulesets'); ?>"><span class="dashicons dashicons-trash pleb_icon"></span></a>
            </div>
            
        </div>
    </div>

    <div class="postbox-header" style="padding:8px 12px;justify-content:flex-start;border-bottom:none;">
        
        <label for="<?php echo esc_attr($fieldKey); ?>_<?php echo $this->getId(); ?>_cost" style="display:block;font-weight:600;padding-right:5px;white-space:nowrap;">
            <?php esc_attr_e("Price to apply:", 'pleb-shipping-rulesets'); ?>
        </label>

        <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][cost]" value="<?php echo $this->getCost(); ?>" class="" placeholder="<?php esc_attr_e("", 'pleb-shipping-rulesets'); ?>" id="<?php echo esc_attr($fieldKey); ?>_<?php echo $this->getId(); ?>_cost" />

        <?php echo wc_help_tip(
	sprintf(
		__("Works the same as %s setting field", 'pleb-shipping-rulesets'),
		'<b>'.__('Base price', 'pleb-shipping-rulesets').'</b>'
	),
	true
); ?>

    </div>
</div>