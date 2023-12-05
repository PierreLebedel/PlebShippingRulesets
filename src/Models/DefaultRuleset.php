<?php

namespace PlebWooCommerceShippingRulesets\Models;

class DefaultRuleset extends Ruleset
{
    protected $order = 'default';

    public function htmlRender(string $fieldKey): string
    {
        ob_start();

        ?><div class="postbox pleb_ruleset" style="margin-bottom:15px;">

            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">
            <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][order]" value="default">

            <div class="postbox-header pleb_title_input_wrapper" style="padding:4px 0;">
                <h2 class="hndle" style="cursor:auto;">
                    <span>
                        <?php echo $this->getName(); ?> - <?php _e("Default", 'pleb'); ?>
                    </span>
                </h2>
                
                <div class="pleb_input_wrapper" style="display:none;height:30px;padding:3px 12px;">
                    <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][name]" value="<?php echo esc_attr($this->getName()); ?>" placeholder="<?php esc_attr_e("Ruleset name", "pleb"); ?>" required>
                </div>
                
                <div class="handle-actions" style="padding-right:12px;">
                    <button class="button button-small pleb_edit_ruleset_button">
                        <span class="button_dynamic_action"><?php _e("Edit", 'pleb'); ?></span>
                        <span class="button_dynamic_action" style="display:none;"><?php _e("Stop editing", 'pleb'); ?></span>
                    </button>

                    <div class="plugins" style="float:right;padding-top:5px;padding-left:5px;">
                        <a href="#" class="delete pleb_ruleset_default_delete" data-confirm="<?php esc_attr_e("Are you sure to delete this ruleset?", 'pleb'); ?>" style="text-decoration:none;font-size:11px;"><?php _e("Delete default ruleset", 'pleb'); ?></a>
                    </div>
                    
                </div>
            </div>

            <div class="inside" style="margin:0;padding:8px 12px;display:flex;justify-content:flex-start;align-items:center;">
                
                <label for="" style="display:block;font-weight:600;padding-right:5px;white-space:nowrap;">
                    <?php esc_attr_e("Price to apply:", 'pleb'); ?>
                </label>

                <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][cost]" value="<?php echo $this->getCost(); ?>" class="" placeholder="<?php esc_attr_e("", 'pleb'); ?>" />

                <?php echo wc_help_tip(
                    sprintf(
                        __("Works the same as %s setting field", 'pleb'),
                        '<b>'.__('Base price', 'pleb').'</b>'
                    ),
                    true
                ); ?>

            </div>
        </div><?php

                return ob_get_clean();
    }

    public function matchToWooCommercePackageArray(array $package = [], int $methodInstanceId = 0): bool
    {
        return true;
    }

}
