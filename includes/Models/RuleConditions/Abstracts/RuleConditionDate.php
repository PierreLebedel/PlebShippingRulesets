<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleCondition;

abstract class RuleConditionDate extends RuleCondition
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
		?><input type="date" name="<?php echo esc_attr($fieldName); ?>" value="<?php esc_attr_e($value); ?>" class="pleb_w100" required maxlength="10" /><?php
		return ob_get_clean();
	}
}
