<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups;

use PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups\RuleConditionsGroup;

class UserGroup extends RuleConditionsGroup
{
	public function getName(): string
	{
		return __("User", 'pleb-woocommerce-shipping-rulesets');
	}
}
