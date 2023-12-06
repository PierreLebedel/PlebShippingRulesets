<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumericInteger;

class RuleConditionCartItemByShippingClassCount extends RuleConditionNumericInteger
{
	public function getId(): string
	{
		return 'cart_item_shipping_class_count';
	}

	public function getName(): string
	{
		return __("Cart item quantity (shipping class)", 'pleb');
	}

	public function getVariants(): array
	{
		$classes = [];

		$wooShippingClasses = get_terms([
			'taxonomy' => 'product_shipping_class',
			'hide_empty' => false,
		]);
		//dd($wooShippingClasses);

		if(!empty($wooShippingClasses)) {
			foreach($wooShippingClasses as $termObject) {
				$classes[$termObject->slug] = sprintf(__("Cart item quantity from %s class", 'pleb'),  $termObject->name);
			}
		}

		return $classes;
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

		$package_quantity = 0;
		foreach ($package['contents'] as $values) {
			if ($values['quantity'] > 0 && $values['data']->needs_shipping()) {
				$shippingClassSlug = $values['data']->get_shipping_class();
				if($shippingClassSlug && $shippingClassSlug==$rule->getConditionVariant() ){
					$package_quantity += intval($values['quantity']);
				}
			}
		}

		switch ($conditionComparator) {
			case '<':
				if ($package_quantity < $conditionValue) {
					return true;
				}
				break;
			case '<=':
				if ($package_quantity <= $conditionValue) {
					return true;
				}
				break;
			case '=':
				if ($package_quantity == $conditionValue) {
					return true;
				}
				break;
			case '>=':
				if ($package_quantity >= $conditionValue) {
					return true;
				}
				break;
			case '>':
				if ($package_quantity > $conditionValue) {
					return true;
				}
				break;
		}

		return false;
	}
}
