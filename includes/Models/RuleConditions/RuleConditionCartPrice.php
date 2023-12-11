<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumericFloat;

class RuleConditionCartPrice extends RuleConditionNumericFloat
{
	public function getId(): string
	{
		return 'cart_price';
	}

	public function getName(): string
	{
		return __("Cart price", 'pleb-woocommerce-shipping-rulesets');
	}

	public function getVariants(): array
	{
		return [
			'tax_exclude' => __("Cart price tax exclusive", 'pleb-woocommerce-shipping-rulesets'),
			'tax_include' => __("Cart price tax inclusive", 'pleb-woocommerce-shipping-rulesets'),
		];
	}

	public function extractValueFromWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): mixed
	{
		$package_cost = ($rule->getConditionVariant() == 'tax_include') ? $package['cart_subtotal'] : $package['contents_cost'];
		return floatval($package_cost);
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
		$conditionValue = floatval($conditionValue);

		$package_cost = $this->extractValueFromWooCommercePackageArray($package, $rule, $methodInstanceId);

		switch ($conditionComparator) {
			case '<':
				if ($package_cost < $conditionValue) {
					return true;
				}
				break;
			case '<=':
				if ($package_cost <= $conditionValue) {
					return true;
				}
				break;
			case '=':
				if ($package_cost == $conditionValue) {
					return true;
				}
				break;
			case '>=':
				if ($package_cost >= $conditionValue) {
					return true;
				}
				break;
			case '>':
				if ($package_cost > $conditionValue) {
					return true;
				}
				break;
		}

		return false;
	}
}
