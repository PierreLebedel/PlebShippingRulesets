<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleCondition;

abstract class RuleConditionNumeric extends RuleCondition
{
    
    public function getComparators(): array
    {
        return [
            '<',
            '<=',
            '=',
            '>=',
            '>',
        ];
    }

    public function getType(): string
    {
        return 'numeric';
    }

}
