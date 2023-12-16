<?php

namespace PlebShippingRulesets\Models\RuleConditions;

use PlebShippingRulesets\Contracts\RuleInterface;
use PlebShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebShippingRulesets\Models\RuleConditionsGroups\DateTimeGroup;
use PlebShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionDate;

class RuleConditionTodayDate extends RuleConditionDate
{
	public function getId(): string
	{
		return 'today_date';
	}

	public function getName(): string
	{
		return __("Current date", 'pleb-shipping-rulesets');
	}

	public function getGroup(): ?RuleConditionsGroupInterface
	{
		return new DateTimeGroup();
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

		$packageValue = wp_date('Y-m-d');

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
