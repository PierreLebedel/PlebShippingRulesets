<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleCondition;

abstract class RuleConditionTime extends RuleCondition
{
	public function getComparators(): array
	{
		return [
			'<'  => "<",
			'<=' => "<=",
			'='  => "=",
			'!=' => "!=",
			'>=' => ">=",
			'>'  => ">",
		];
	}

	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><input type="time" name="<?php echo esc_attr($fieldName); ?>" value="<?php esc_attr_e($value); ?>" class="pleb_w100" required min="00:00" max="23:59" maxlength="5" /><?php
		return ob_get_clean();
	}
}
