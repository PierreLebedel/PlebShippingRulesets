<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionText;

class RuleConditionCoupon extends RuleConditionText
{
	public function getId(): string
	{
		return 'coupon';
	}

	public function getName(): string
	{
		return __("Coupon", 'pleb-woocommerce-shipping-rulesets');
	}

	// public function getVariants(): array
	// {
	// 	return [
	// 		'present' => __("Coupon is present", 'pleb-woocommerce-shipping-rulesets'),
	// 		'absent'  => __("Coupon is absent", 'pleb-woocommerce-shipping-rulesets'),
	// 	];
	// }

	public function getComparators(): array
	{
		return [
			'=' => __("is present", 'pleb-woocommerce-shipping-rulesets'),
			'!=' => __("is absent", 'pleb-woocommerce-shipping-rulesets'),
		];
	}

	private function cleanTextField(string $text): string
	{
		$text = strtolower($text);
		$text = trim($text);
		return $text;
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
		$conditionValue = $this->cleanTextField($conditionValue);

		$cleanedArray = [];
		if(array_key_exists('applied_coupons', $package) && is_array($package['applied_coupons'])) {
			foreach($package['applied_coupons'] as $coupon) {
				$cleanedArray[] = $this->cleanTextField($coupon);
			}
		}

		// if( $rule->getConditionVariant()=='present' && in_array($conditionValue, $cleanedArray) ){
		// 	return true;
		// }

		// if( $rule->getConditionVariant()=='absent' && !in_array($conditionValue, $cleanedArray) ){
		// 	return true;
		// }

		if($conditionComparator == '=' && in_array($conditionValue, $cleanedArray, true)) {
			return true;
		}

		if($conditionComparator == '!=' && !in_array($conditionValue, $cleanedArray, true)) {
			return true;
		}

		return false;
	}
}
