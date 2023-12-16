<?php

namespace PlebShippingRulesets\Models\RuleConditionsGroups;

use PlebShippingRulesets\Models\RuleConditionsGroups\RuleConditionsGroup;

class UserGroup extends RuleConditionsGroup
{
	public function getName(): string
	{
		return __("User", 'pleb-shipping-rulesets');
	}
}
