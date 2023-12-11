<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups\UserGroup;
use PlebWooCommerceShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionBoolean;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionChoices;

class RuleConditionUserHasRole extends RuleConditionChoices
{
	public function getId(): string
	{
		return 'user_role';
	}

	public function getName(): string
	{
		return __("User role", 'pleb-woocommerce-shipping-rulesets');
	}

	public function getGroup(): ?RuleConditionsGroupInterface
	{
		return new UserGroup();
	}

	public function getComparators(): array
	{
		return [
			'=' => _x("is", "User role is/isn't", 'pleb-woocommerce-shipping-rulesets'),
			'!=' => _x("isn't", "User role is/isn't", 'pleb-woocommerce-shipping-rulesets'),
		];
	}

	public function getChoices(): array
	{
		global $wp_roles;
		$rolesArray = apply_filters('pwsr_get_all_roles', $wp_roles->roles);

		$rolesChoices = [];
		foreach($rolesArray as $k=>$v){
			$rolesChoices[$k] = translate_user_role($v['name'], 'pleb-woocommerce-shipping-rulesets');
		}

		return $rolesChoices;
	}

	public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
	{
		$conditionComparator = $rule->getConditionComparator();
		if (is_null($conditionComparator)) {
			return false;
		}
		
		$conditionValue = $rule->getConditionValue();
		if (is_null($conditionValue)) {
			return false;
		}

		$user = wp_get_current_user();
		if(!$user->exists()) return false;

		if( $conditionComparator=='=' && in_array($conditionValue, $user->roles) ){
			return true;
		}

		if( $conditionComparator=='!=' && !in_array($conditionValue, $user->roles) ){
			return true;
		}

		return false;
	}
}
