<?php

namespace PlebShippingRulesets\Models\RuleConditions;

use PlebShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebShippingRulesets\Contracts\RuleInterface;
use PlebShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionChoices;
use PlebShippingRulesets\Models\RuleConditionsGroups\DateTimeGroup;

class RuleConditionDayOfMonth extends RuleConditionChoices
{
	public function getId(): string
	{
		return 'day_of_month';
	}

	public function getName(): string
	{
		return __("Day of month", 'pleb-shipping-rulesets');
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
			'>=' => ">=",
			'>'  => ">",
		];
	}

	public function getChoices(): array
	{
		$days = [];
		for($i = 1; $i <= 31; $i++) {
			$days[$i] = $i;
		}
		return $days;
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

		$packageValue = intval(wp_date('j'));

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
