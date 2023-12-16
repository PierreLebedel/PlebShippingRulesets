<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleCondition;

abstract class RuleConditionChoices extends RuleCondition
{
	public function getChoices(): array
	{
		return [];
	}

	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><select name="<?php echo esc_attr($fieldName); ?>" class="pleb_w100" required>
			<option value="" <?php selected(empty($value)); ?> disabled><?php _e("Choose an option", 'pleb-woocommerce-shipping-rulesets'); ?></option>
			<?php foreach($this->getChoices() as $k => $v): ?>
				<option value="<?php esc_attr_e($k); ?>" <?php selected($value == $k); ?> ><?php echo $v; ?></option>
			<?php endforeach; ?>
		</select><?php
		return ob_get_clean();
	}
}
