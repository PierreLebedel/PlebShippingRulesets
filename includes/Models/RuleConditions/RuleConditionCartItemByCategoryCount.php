<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumericInteger;

class RuleConditionCartItemByCategoryCount extends RuleConditionNumericInteger
{
	public function getId(): string
	{
		return 'cart_item_category_count';
	}

	public function getName(): string
	{
		return __("Cart item quantity (category)", 'pleb');
	}

	public function getVariants(): array
	{
		$classes = [];

		$wooCategories = get_terms([
			'taxonomy' => 'product_cat',
			'hide_empty' => false,
		]);
		//dd($wooCategories);

		if(!empty($wooCategories)) {
			foreach($wooCategories as $termObject) {
				$classes[$termObject->slug] = sprintf(__("Cart item quantity from %s category", 'pleb'), $termObject->name);
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
				$shippingClassSlug = get_the_terms($values['data']->get_id(), 'product_cat');
				if(!empty($shippingClassSlug)) {
					$conditionTermSlug = $rule->getConditionVariant();
					foreach($shippingClassSlug as $termObject) {
						if($termObject->slug == $conditionTermSlug) {
							$package_quantity += intval($values['quantity']);
						}
					}
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
