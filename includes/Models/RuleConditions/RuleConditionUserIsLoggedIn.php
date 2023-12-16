<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionBoolean;
use PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups\UserGroup;

class RuleConditionUserIsLoggedIn extends RuleConditionBoolean
{
	public function getId(): string
	{
		return 'user_is_logged_in';
	}

	public function getName(): string
	{
		return __("User is logged in", 'pleb-woocommerce-shipping-rulesets');
	}

	public function getGroup(): ?RuleConditionsGroupInterface
	{
		return new UserGroup();
	}

	public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
	{
		$conditionValue = $rule->getConditionValue();
		if (is_null($conditionValue)) {
			return false;
		}
		$conditionValue = boolval($conditionValue);

		$user_is_logged_in = (bool) is_user_logged_in();

		if($conditionValue && $user_is_logged_in) {
			return true;
		}

		if($conditionValue && !$user_is_logged_in) {
			return true;
		}

		return false;
	}
}
