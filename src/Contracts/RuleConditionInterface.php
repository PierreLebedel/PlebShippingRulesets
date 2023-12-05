<?php

namespace PlebWooCommerceShippingRulesets\Contracts;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;

interface RuleConditionInterface
{
    public function getId(): string;

    public function getName(): string;

    public function getVariants(): array;

    public function getComparators(): array;

    public function getInputHtml(string $fieldName, mixed $value): string;

    public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool;

}
