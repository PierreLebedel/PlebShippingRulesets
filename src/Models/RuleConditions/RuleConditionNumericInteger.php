<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumeric;

abstract class RuleConditionNumericInteger extends RuleConditionNumeric
{

    public function getInputHtml(string $fieldName, mixed $value): string
    {
        ob_start();
		?><input type="number" step="1" name="<?php echo esc_attr($fieldName); ?>" value="<?php esc_attr_e($value); ?>" class="w-100" required /><?php
		return ob_get_clean();
    }

}
