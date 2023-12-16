<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups\DateTimeGroup;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionChoices;

class RuleConditionMonthOfYear extends RuleConditionChoices
{
	public function getId(): string
	{
		return 'month_of_year';
	}

	public function getName(): string
	{
		return __("Month of year", 'pleb-woocommerce-shipping-rulesets');
	}

	public function getGroup(): ?RuleConditionsGroupInterface
	{
		return new DateTimeGroup();
	}

	public function getComparators(): array
	{
		return [
			'<'  => "<",
			'<=' => "<=",
			'='  => "=",
			'!='  => "!=",
			'>=' => ">=",
			'>'  => ">",
		];
	}

	public function getChoices(): array
	{
		/**
		 * @global \WP_Locale $wp_locale WordPress date and time locale object.
		 */
		global $wp_locale;

		$months = [];

		for($i = 1; $i <= 12; $i++) {
			$months[$i] = ucfirst($wp_locale->get_month($i));
		}

		return $months;
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

		$packageValue = intval(wp_date('m'));

		switch ($conditionComparator) {
			case '<':
				if ($packageValue < $conditionValue) {
					return true;
				}
				break;
			case '<=':
				if ($packageValue <= $conditionValue) {
					return true;
				}
				break;
			case '=':
				if ($packageValue == $conditionValue) {
					return true;
				}
				break;
			case '!=':
				if ($packageValue != $conditionValue) {
					return true;
				}
				break;
			case '>=':
				if ($packageValue >= $conditionValue) {
					return true;
				}
				break;
			case '>':
				if ($packageValue > $conditionValue) {
					return true;
				}
				break;
		}

		return false;
	}
}
