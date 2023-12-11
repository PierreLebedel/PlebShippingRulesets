<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumericInteger;

class RuleConditionCartWeight extends RuleConditionNumericInteger
{
	public function getId(): string
	{
		return 'cart_weight';
	}

	public function getName(): string
	{
		return sprintf(__("Cart weight (%s)", 'pleb-woocommerce-shipping-rulesets'), __(get_option('woocommerce_weight_unit'), 'woocommerce'));
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
		$conditionValue = intval($conditionValue);

		$package_weight = 0;
		//dump($package['contents']);
		foreach ($package['contents'] as $values) {
			if($values['data']->needs_shipping()) {
				$productWeight = $values['data']->get_weight();
				if(is_numeric($productWeight)) {
					$cartItemWeight = $values['quantity'] * $productWeight;
					$package_weight += $cartItemWeight;
				}
			}

		}

		//dd($package_weight);

		switch ($conditionComparator) {
			case '<':
				if ($package_weight < $conditionValue) {
					return true;
				}
				break;
			case '<=':
				if ($package_weight <= $conditionValue) {
					return true;
				}
				break;
			case '=':
				if ($package_weight == $conditionValue) {
					return true;
				}
				break;
			case '>=':
				if ($package_weight >= $conditionValue) {
					return true;
				}
				break;
			case '>':
				if ($package_weight > $conditionValue) {
					return true;
				}
				break;
		}

		return false;
	}
}