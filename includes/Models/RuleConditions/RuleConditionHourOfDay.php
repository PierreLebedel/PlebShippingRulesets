<?php

namespace PlebShippingRulesets\Models\RuleConditions;

use PlebShippingRulesets\Contracts\RuleInterface;
use PlebShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebShippingRulesets\Models\RuleConditionsGroups\DateTimeGroup;
use PlebShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionTime;

class RuleConditionHourOfDay extends RuleConditionTime
{
	public function getId(): string
	{
		return 'current_time';
	}

	public function getName(): string
	{
		return __("Current time", 'pleb-shipping-rulesets');
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

		$packageValue = wp_date('H:i');

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
