<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionBoolean;

class RuleConditionUserHasRole extends RuleConditionBoolean
{
	public function getId(): string
	{
		return 'user_has_role';
	}

	public function getName(): string
	{
		return __("User has role", 'pleb-woocommerce-shipping-rulesets');
	}

	public function getVariants(): array
	{
		global $wp_roles;
		$rolesArray = apply_filters('pwsr_get_all_roles', $wp_roles->roles);

		$rolesChoices = [];
		foreach($rolesArray as $k=>$v){
			$rolesChoices[$k] = __("User has role", 'pleb-woocommerce-shipping-rulesets').' '.translate_user_role($v['name'], 'pleb-woocommerce-shipping-rulesets');
		}

		return $rolesChoices;
	}

	public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
	{
		$conditionValue = $rule->getConditionValue();
		if (is_null($conditionValue)) {
			return false;
		}
		$conditionValue = boolval($conditionValue);

		$user = wp_get_current_user();
		if(!$user->exists()) return false;

		if( !empty($user->roles) && is_array($user->roles) ){

			if( $conditionValue ){
				return in_array($rule->getConditionVariant(), $user->roles);
			}

			if( !$conditionValue ){
				return !in_array($rule->getConditionVariant(), $user->roles);
			}

		}

		return false;
	}
}
