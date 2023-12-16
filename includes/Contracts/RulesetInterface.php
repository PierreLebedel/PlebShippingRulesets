<?php

namespace PlebShippingRulesets\Contracts;

interface RulesetInterface
{
	public static function createFromArray(array $rulesetArray = []): self;

	public function getId(): string;

	public function getName(): string;

	public function getCost(): string;

	public function getOrder(): mixed;

	public function getRules(): array;

	public function isDefault(): bool;

	public function htmlRender(string $fieldKey): string;

	public function matchToWooCommercePackageArray(array $package = [], int $methodInstanceId = 0): bool;
}
