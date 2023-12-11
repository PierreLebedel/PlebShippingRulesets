<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionNumeric;

abstract class RuleConditionNumericInteger extends RuleConditionNumeric
{
	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><input type="number" step="1" name="<?php echo esc_attr($fieldName); ?>" value="<?php esc_attr_e($value); ?>" class="pleb_w100" required /><?php
		return ob_get_clean();
	}
}
