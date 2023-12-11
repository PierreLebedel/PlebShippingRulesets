<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups;

use PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups\RuleConditionsGroup;

class DateTimeGroup extends RuleConditionsGroup
{
	public function getName(): string
	{
		return __("Date & time", 'pleb-woocommerce-shipping-rulesets');
	}
}
