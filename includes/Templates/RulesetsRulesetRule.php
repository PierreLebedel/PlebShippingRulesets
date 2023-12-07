<tr class="pleb_rule active" data-field_key="<?php echo esc_attr($fieldKey); ?>" data-rule_id="<?php echo $this->getId(); ?>">
    <td class="">
        <!-- <strong><?php _e("Rule", 'pleb'); ?> #<?php echo $this->getId(); ?></strong> -->
        <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">

        <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_id]" required class="rule_condition_id pleb_w100">
            <option value="" selected disabled><?php _e("Choose an option", 'pleb'); ?></option>
            <?php foreach ($allRuleConditions as $rc_id => $rc) : ?>
                <?php if (!empty($rc->getVariants())) : ?>
                <optgroup label="<?php esc_attr_e($rc->getName()); ?>">
                    <?php foreach ($rc->getVariants() as $k => $v) : ?>
                    <option value="<?php echo $rc_id.':'.$k; ?>" <?php selected($this->getConditionId() == $rc_id.':'.$k); ?>><?php _e($v, 'pleb'); ?></option>
                    <?php endforeach; ?>
                </optgroup>
                <?php else : ?>
                <option value="<?php echo $rc_id; ?>" <?php selected($this->getConditionId() == $rc_id); ?>><?php echo $rc->getName(); ?></option>
                <?php endif; ?>
            <?php endforeach; ?>
        </select>
    </td>

    <?php if ($condition = $this->getCondition()) : ?>
    <td class="pleb_shrink">
        <?php if (!empty($condition->getComparators())) : ?>
        <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_comparator]" required class="">
            <option value="" <?php selected(is_null($this->getConditionComparator())); ?> disabled><?php _e("...", 'pleb'); ?></option>
            <?php foreach ($condition->getComparators() as $display) : ?>
            <option value="<?php echo $display; ?>" <?php selected($this->getConditionComparator() == $display || count($condition->getComparators()) == 1); ?>><?php echo $display; ?></option>
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
        <?php _e("Please choose the condition", 'pleb'); ?>
    </td>
    <?php endif; ?>

    <td class="pleb_shrink" style="text-align:right;">
        <a href="#" class="pleb_linkdanger pleb_rule_delete" data-rule_id="<?php echo $this->getId(); ?>" data-confirm="<?php esc_attr_e("Are you sure to delete this rule?", 'pleb'); ?>"><nobr><?php esc_attr_e("Delete", 'pleb'); ?></nobr></a>
    </td>
</tr>