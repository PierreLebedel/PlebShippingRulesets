<?php

namespace PlebWooCommerceShippingRulesets\Models;

use PlebWooCommerceShippingRulesets\Models\RuleCondition;

class Rule
{
    private $id;
    private $condition_id = null;
    private $condition_comparator = null;
    private $condition_value = null;

    public function __construct()
    {
    }

    public static function create()
    {
        $instance = new self();
        $instance->setId(uniqid());
        return $instance;
    }

    public static function createFromArray(array $ruleArray): self
    {
        $instance = new self();
        $instance->setId($ruleArray['id']);
        $instance->setConditionId($ruleArray['condition_id']);
        $instance->setConditionComparator($ruleArray['condition_comparator']);
        $instance->setConditionValue($ruleArray['condition_value']);
        return $instance;
    }

    public function setId(string $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getId(): string
    {
        if(empty($this->id)) {
            return uniqid();
        }
        return $this->id;
    }

    public function setConditionId(?string $conditionId): self
    {
        $this->condition_id = $conditionId;
        return $this;
    }

    public function getConditionId(): ?string
    {
        return $this->condition_id;
    }

    public function getCondition(): ?RuleCondition
    {
        return RuleCondition::find($this->condition_id);
    }

    public function setConditionComparator(?string $conditionComparator): self
    {
        $condition = $this->getCondition();

        if($condition){
            if( in_array($conditionComparator, $condition->getComparators()) ){
                $this->condition_comparator = $conditionComparator;
            }
        }
        
        return $this;
    }

    public function getConditionComparator(): ?string
    {
        return $this->condition_comparator;
    }

    public function setConditionValue(?string $value): self
    {
        $this->condition_value = $value;
        return $this;
    }

    public function getConditionValue(): ?string
    {
        return $this->condition_value;
    }

    // public function getRuleset(): Ruleset
    // {
    //     return new Ruleset('test read from child');
    // }

    public function htmlRender(string $fieldKey): string
    {
        ob_start();

        ?><tr class="pleb_rule inactive">
            <td>
                <!-- <strong><?php _e("Rule", 'pleb'); ?> #<?php echo $this->getId(); ?></strong> -->
                <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">

                <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_id]" required>
                    <option value="" <?php selected(is_null($this->getConditionId())); ?> disabled><?php _e("Choose an option", 'pleb'); ?></option>
                    <?php foreach(RuleCondition::all() as $rc_id=>$rc): ?>
                        <option value="<?php echo $rc_id; ?>" <?php selected($this->getConditionId() == $rc_id); ?>><?php echo $rc->getName(); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>

            <?php if($condition = $this->getCondition()): ?>
            <td>
                <?php if( !empty($condition->getComparators()) ): ?>
                <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_comparator]" required>
                    <option value="" <?php selected(is_null($this->getConditionComparator())); ?> disabled><?php _e("...", 'pleb'); ?></option>
                    <?php foreach($condition->getComparators() as $display): ?>
                    <option value="<?php echo $display; ?>" <?php selected($this->getConditionComparator() == $display); ?>><?php echo $display; ?></option>
                <?php endforeach; ?>
                </select>
                <?php else: ?>
                <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_comparator]" value="">
                <?php endif; ?>
            </td>
            <td class="w-100">
                <?php $valueType = $condition->getType();
                if($valueType=='none'): ?>
                <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_value]" value="">
                <?php elseif($valueType=='number'): ?>
                <input type="number" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_value]" value="<?php echo $this->getConditionValue(); ?>" class="w-100" required>
                <?php else: ?>
                <input type="text" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_value]" value="<?php echo $this->getConditionValue(); ?>" class="w-100" required>
                <?php endif; ?>
            </td>
            <?php else: ?>
            <td colspan="2">
                <?php _e("Please choose the condition", 'pleb'); ?>
            </td>
            <?php endif; ?>
            <td>
                <a href="#" class="delete pleb_rule_delete" data-rule_id="<?php echo $this->getId(); ?>" data-confirm="<?php esc_attr_e("Are you sure?", 'pleb'); ?>" style="float:right;margin-top:6px;"><?php _e("Delete rule", 'pleb'); ?></a>
            </td>
        </tr><?php

        return ob_get_clean();
    }


    
}