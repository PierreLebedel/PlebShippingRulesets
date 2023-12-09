<div class="postbox pleb_ruleset">

    <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
    <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="<?php echo esc_attr($this->getOrder()); ?>">

    <div class="postbox-header pleb_title_input_wrapper" style="padding:4px 0;">
        <h2 class="hndle" title="<?php esc_attr_e("Move up/down to change ruleset priority", 'pleb-woocommerce-shipping-rulesets'); ?>">
            <span>
                <span class="dashicons dashicons-move"></span>
                <?php echo $this->getName(); ?>
            </span>
        </h2>
        
        <div class="pleb_input_wrapper" style="display:none;height:30px;padding:3px 12px;">
            <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" placeholder="<?php esc_attr_e("Ruleset name", 'pleb-woocommerce-shipping-rulesets'); ?>" required>
        </div>
        
        <div class="handle-actions" style="padding-right:12px;">
            <button class="button pleb_edit_ruleset_button">
                <span class="button_dynamic_action"><span class="dashicons dashicons-edit pleb_icon"></span><?php _e("Edit", 'pleb-woocommerce-shipping-rulesets'); ?></span>
                <span class="button_dynamic_action" style="display:none;"><span class="dashicons dashicons-saved pleb_icon"></span><?php _e("Stop editing", 'pleb-woocommerce-shipping-rulesets'); ?></span>
            </button>
        </div>
    </div>

    <div class="postbox-header" style="padding:8px 12px;justify-content:flex-start;">
        
        <label for="<?php echo esc_attr($fieldKey); ?>_<?php echo $this->getId(); ?>_cost" style="display:block;font-weight:600;padding-right:5px;white-space:nowrap;">
            <?php esc_attr_e("Price to apply:", 'pleb-woocommerce-shipping-rulesets'); ?>
        </label>

        <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][cost]" value="<?php echo $this->getCost(); ?>" class="" placeholder="<?php esc_attr_e("", 'pleb-woocommerce-shipping-rulesets'); ?>" id="<?php echo esc_attr($fieldKey); ?>_<?php echo $this->getId(); ?>_cost" />

        <?php echo wc_help_tip(sprintf(
	__("Works the same as %s setting field", 'pleb-woocommerce-shipping-rulesets'),
	'<b>'.__('Base price', 'pleb-woocommerce-shipping-rulesets').'</b>'
), true); ?>

    </div>

    <div class="inside" style="margin-bottom:0;">

        <?php $rules = $this->getRules(); ?>

        <div class="pleb_no_ruleset_rule_notice notice notice-info inline notice-alt pleb_notice" style=";<?php if (!empty($rules)): ?>display:none;<?php endif; ?>">
            <p><span class="dashicons dashicons-dismiss"></span> <?php _e("No rule in this ruleset yet.", 'pleb-woocommerce-shipping-rulesets'); ?></p>
        </div>

        <table class="widefat plugins ruleset_rules" style="margin:10px 0;<?php if (empty($rules)) {
            echo 'display:none;';
        } ?>">
            <?php foreach ($rules as $rule) : ?>
                <?php echo $rule->htmlRender($fieldKey.'['.$this->getId().'][rules]');?>
            <?php endforeach; ?>
        </table>

        <div style="display:flex;align-items:center;justify-content:space-between">
            <button type="button" class="button pleb_ruleset_add_rule_button" data-field_key="<?php echo $fieldKey.'['.$this->getId().'][rules]'; ?>"><span class="dashicons dashicons-plus pleb_icon"></span><?php _e("Add new rule", 'pleb-woocommerce-shipping-rulesets'); ?></button>

            <div>
                <button class="button pleb_duplicate_ruleset_button" data-ruleset_id="<?php echo $this->getId(); ?>">
                    <span class="dashicons dashicons-controls-repeat pleb_icon"></span><?php _e("Duplicate", 'pleb-woocommerce-shipping-rulesets'); ?>
                </button>

                <div style="float:right;padding-top:6px;padding-left:5px;">
                    <a href="#" class="pleb_linkdanger pleb_ruleset_delete" data-ruleset_id="<?php echo $this->getId(); ?>" data-confirm="<?php esc_attr_e("Are you sure to delete this ruleset and all of its rules?", 'pleb-woocommerce-shipping-rulesets'); ?>" title="<?php esc_attr_e("Delete", 'pleb-woocommerce-shipping-rulesets'); ?>"><span class="dashicons dashicons-trash pleb_icon"></span></a>
                </div>
            </div>
        </div>
        
        

    </div>
</div>