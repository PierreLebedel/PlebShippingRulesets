<?php

namespace PlebShippingRulesets\Models\RuleConditions;

use PlebShippingRulesets\Contracts\RuleInterface;
use PlebShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebShippingRulesets\Models\RuleConditionsGroups\DateTimeGroup;
use PlebShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionChoices;
use PlebShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionNumericInteger;

class RuleConditionYear extends RuleConditionNumericInteger
{
	public function getId(): string
	{
		return 'year';
	}

	public function getName(): string
	{
		return __("Current year", 'pleb-shipping-rulesets');
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
			'!=' => "!=",
			'>=' => ">=",
			'>'  => ">",
		];
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

		$packageValue = intval(wp_date('Y'));

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
