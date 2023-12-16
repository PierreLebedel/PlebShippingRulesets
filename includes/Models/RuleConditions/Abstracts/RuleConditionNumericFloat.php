<?php

namespace PlebShippingRulesets\Models\RuleConditions\Abstracts;

use PlebShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionNumeric;

abstract class RuleConditionNumericFloat extends RuleConditionNumeric
{
	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><input type="number" step="0.01" name="<?php echo esc_attr($fieldName); ?>" value="<?php esc_attr_e($value); ?>" class="pleb_w100" required /><?php
		return ob_get_clean();
	}
}
