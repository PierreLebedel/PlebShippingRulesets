<?php

namespace PlebShippingRulesets\Models\RuleConditionsGroups;

use PlebShippingRulesets\Models\RuleConditionsGroups\RuleConditionsGroup;

class DateTimeGroup extends RuleConditionsGroup
{
	public function getName(): string
	{
		return __("Date & time", 'pleb-shipping-rulesets');
	}
}
