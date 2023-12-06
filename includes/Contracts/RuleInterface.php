<?php

namespace PlebWooCommerceShippingRulesets\Contracts;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;

interface RuleInterface
{
	public static function createFromArray(array $ruleArray): self;

	public function getId(): string;

	public function getConditionId(): ?string;

	public function getCondition(): ?RuleConditionInterface;

	public function getConditionVariant(): ?string;

	public function getConditionComparator(): ?string;

	public function getConditionValue(): ?string;

	public function htmlRender(string $fieldKey): string;

	public function matchToWooCommercePackageArray(array $package = [], int $methodInstanceId = 0): bool;
}
