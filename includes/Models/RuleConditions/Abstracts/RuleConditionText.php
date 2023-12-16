<?php

namespace PlebShippingRulesets\Models\RuleConditions\Abstracts;

use PlebShippingRulesets\Models\RuleConditions\Abstracts\RuleCondition;

abstract class RuleConditionText extends RuleCondition
{
	public function getComparators(): array
	{
		return [
			'=' => "=",
			'!=' => "!=",
		];
	}

	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><input type="text" name="<?php echo esc_attr($fieldName); ?>" value="<?php esc_attr_e($value); ?>" class="pleb_w100" required /><?php
		return ob_get_clean();
	}
}
