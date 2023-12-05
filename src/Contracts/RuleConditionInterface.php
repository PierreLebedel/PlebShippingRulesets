<?php

namespace PlebWooCommerceShippingRulesets\Contracts;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;

interface RuleConditionInterface
{
    public function getId(): string;

    public function getName(): string;

    public function getVariants(): array;

    public function getComparators(): array;

    public function getType(): string;

    public function matchToWooCommercePackageArray(RuleInterface $rule, array $package = [], ?RulesShippingMethod $method = null): bool;

}
