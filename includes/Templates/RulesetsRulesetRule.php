<tr class="pleb_rule active" data-field_key="<?php echo esc_attr($fieldKey); ?>" data-rule_id="<?php echo $this->getId(); ?>">
    <td class="">
        <!-- <strong><?php _e("Rule", 'pleb-woocommerce-shipping-rulesets'); ?> #<?php echo $this->getId(); ?></strong> -->
        <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">

        <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_id]" required class="rule_condition_id pleb_w100">
            <option value="" selected disabled><?php _e("Choose an option", 'pleb-woocommerce-shipping-rulesets'); ?></option>
            <?php
            $currentGroup = '';
            foreach ($allRuleConditions as $rc_id => $rc) : ?>
                <?php if (!empty($rc->getVariants())) : ?>
                <optgroup label="<?php echo esc_attr($rc->getName()); ?>">
                    <?php foreach ($rc->getVariants() as $k => $v) : ?>
                    <option value="<?php echo $rc_id.':'.$k; ?>" <?php selected($this->getConditionId() == $rc_id.':'.$k); ?>><?php echo $v; ?></option>
                    <?php endforeach; ?>
                </optgroup>

                <?php elseif(method_exists($rc, 'getGroupName')): ?>
                    <?php if(!empty($currentGroup) && $currentGroup != $rc->getGroupName()): ?>
                        </optgroup>
                        <?php $currentGroup = ''; ?>
                    <?php endif; ?>
                    <?php if($currentGroup != $rc->getGroupName()): ?>
                        <optgroup label="<?php echo esc_attr($rc->getGroupName()); ?>">
                        <?php $currentGroup = $rc->getGroupName(); ?>
                    <?php endif; ?>
                    <option value="<?php echo $rc_id; ?>" <?php selected($this->getConditionId() == $rc_id); ?>><?php echo $rc->getName(); ?></option>

                <?php else : ?>
                    <?php if(!empty($currentGroup)): ?>
                        </optgroup>
                        <?php $currentGroup = ''; ?>
                    <?php endif; ?>
                    <option value="<?php echo $rc_id; ?>" <?php selected($this->getConditionId() == $rc_id); ?>><?php echo $rc->getName(); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>

            <?php if(!empty($currentOpen) && $currentGroup != $rc->getGroupName()): ?>
                </optgroup>
            <?php endif; ?>

        </select>
    </td>

    <?php if ($condition = $this->getCondition()) : ?>
    <td class="pleb_shrink">
        <?php if (!empty($condition->getComparators())) : ?>
        <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_comparator]" required class="">
            <option value="" <?php selected(is_null($this->getConditionComparator())); ?> disabled><?php _e("...", 'pleb-woocommerce-shipping-rulesets'); ?></option>
            <?php foreach ($condition->getComparators() as $k => $display) : ?>
            <option value="<?php echo $k; ?>" <?php selected($this->getConditionComparator() == $k || count($condition->getComparators()) == 1); ?>><?php echo $display; ?></option>
            <?php endforeach; ?>
        </select>
        <?php else : ?>
        <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_comparator]" value="">
        <?php endif; ?>
    </td>
    <td class="">
        <?php echo $condition->getInputHtml(
            	$fieldKey.'['.$this->getId().'][condition_value]',
            	$this->getConditionValue()
            ); ?>
    </td>
    <?php else : ?>
    <td colspan="2">
        <?php _e("Please choose the condition", 'pleb-woocommerce-shipping-rulesets'); ?>
    </td>
    <?php endif; ?>

    <td class="pleb_shrink" style="text-align:right;">
        <a href="#" class="pleb_linkdanger pleb_rule_delete" data-rule_id="<?php echo $this->getId(); ?>" data-confirm="<?php esc_attr_e("Are you sure to delete this rule?", 'pleb-woocommerce-shipping-rulesets'); ?>" title="<?php esc_attr_e("Delete", 'pleb-woocommerce-shipping-rulesets'); ?>"><span class="dashicons dashicons-trash pleb_icon"></span></a>
    </td>
</tr>