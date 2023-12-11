<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleCondition;

abstract class RuleConditionBoolean extends RuleCondition
{
	public function getComparators(): array
	{
		return [];
	}

	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><input type="checkbox" name="<?php echo esc_attr($fieldName); ?>" value="1" <?php checked('1', $value, true); ?> id="checkbox_<?php echo esc_attr($fieldName); ?>" />
		<label for="checkbox_<?php echo esc_attr($fieldName); ?>"><?php _e("Check for true, uncheck for false", 'pleb-woocommerce-shipping-rulesets'); ?></label>
		<?php
		return ob_get_clean();
	}
}
