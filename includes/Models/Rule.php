<?php

namespace PlebWooCommerceShippingRulesets\Models;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;
use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Contracts\RuleConditionInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleCondition;

class Rule implements RuleInterface
{
	private $id;
	private $condition_id = null;
	private $condition_comparator = null;
	private $condition_value = null;

	private function __construct()
	{
	}

	public static function createFromArray(array $ruleArray): self
	{
		$ruleArray = array_merge([
			'id'                   => $id = uniqid(),
			'condition_id'         => null,
			'condition_comparator' => null,
			'condition_value'      => null,
		], $ruleArray);

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
		if (empty($this->id)) {
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

	public function getCondition(): ?RuleConditionInterface
	{
		return RuleCondition::find($this->condition_id);
	}

	public function setConditionComparator(?string $conditionComparator): self
	{
		$condition = $this->getCondition();

		if ($condition && in_array($conditionComparator, $condition->getComparators(), true)) {
			$this->condition_comparator = $conditionComparator;
		} else {
			$this->condition_comparator = null;
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

	public function htmlRender(string $fieldKey): string
	{
		ob_start();

		?><tr class="pleb_rule inactive" data-field_key="<?php echo esc_attr($fieldKey); ?>" data-rule_id="<?php echo $this->getId(); ?>">
            <td>
                <!-- <strong><?php _e("Rule", 'pleb'); ?> #<?php echo $this->getId(); ?></strong> -->
                <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][id]" value="<?php echo $this->getId(); ?>">

                <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_id]" required class="rule_condition_id">
                    <option value="" selected disabled><?php _e("Choose an option", 'pleb'); ?></option>
                    <?php foreach (RuleCondition::all() as $rc_id => $rc) : ?>
                        <?php if (!empty($rc->getVariants())) : ?>
                        <optgroup label="<?php esc_attr_e($rc->getName()); ?>">
                            <?php foreach ($rc->getVariants() as $k => $v) : ?>
                            <option value="<?php echo $rc_id.':'.$k; ?>" <?php selected($this->getConditionId() == $rc_id.':'.$k); ?>><?php echo $rc->getName().' '.__($v, 'pleb'); ?></option>
                            <?php endforeach; ?>
                        </optgroup>
                        <?php else : ?>
                        <option value="<?php echo $rc_id; ?>" <?php selected($this->getConditionId() == $rc_id); ?>><?php echo $rc->getName(); ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </td>

            <?php if ($condition = $this->getCondition()) : ?>
            <td>
                <?php if (!empty($condition->getComparators())) : ?>
                <select name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_comparator]" required>
                    <option value="" <?php selected(is_null($this->getConditionComparator())); ?> disabled><?php _e("...", 'pleb'); ?></option>
                    <?php foreach ($condition->getComparators() as $display) : ?>
                    <option value="<?php echo $display; ?>" <?php selected($this->getConditionComparator() == $display || count($condition->getComparators()) == 1); ?>><?php echo $display; ?></option>
                    <?php endforeach; ?>
                </select>
                <?php else : ?>
                <input type="hidden" name="<?php echo esc_attr($fieldKey); ?>[<?php echo $this->getId(); ?>][condition_comparator]" value="">
                <?php endif; ?>
            </td>
            <td class="w-100">
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
            <td class="w-auto">
                <a href="#" class="delete pleb_rule_delete" data-rule_id="<?php echo $this->getId(); ?>" data-confirm="<?php esc_attr_e("Are you sure to delete this rule?", 'pleb'); ?>" style="font-size:11px;"><nobr><?php esc_attr_e("Delete", 'pleb'); ?></nobr></a>
            </td>
        </tr><?php

		return ob_get_clean();
	}

	public function matchToWooCommercePackageArray(array $package = [], int $methodInstanceId = 0): bool
	{
		$condition = $this->getCondition();

		if ($condition) {
			return $condition->matchToWooCommercePackageArray($package, $this, $methodInstanceId);
		}

		return false;
	}
}
