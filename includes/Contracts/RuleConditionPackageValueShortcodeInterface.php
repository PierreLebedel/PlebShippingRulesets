<?php

namespace PlebWooCommerceShippingRulesets\Contracts;

interface RuleConditionPackageValueShortcodeInterface
{
    public function extractValueFromWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): mixed;
}