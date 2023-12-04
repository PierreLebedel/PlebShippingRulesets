<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Models\RuleCondition;

class RuleConditionCartItemCount extends RuleCondition
{
    
    public function getId(): string
    {
        return 'cart_item_count';
    }

    public function getName(): string
    {
        return __("Cart item quantity", 'pleb');
    }

    public function getComparators(): array
    {
        return [
            '<',
            '<=',
            '=',
            '>=',
            '>'
        ];
    }

    public function getType(): string
    {
        return 'number';
    }

}