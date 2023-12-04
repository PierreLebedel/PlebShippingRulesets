<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Models\RuleCondition;

class RuleConditionCartPrice extends RuleCondition
{
    public function getId(): string
    {
        return 'cart_price';
    }

    public function getName(): string
    {
        return __("Cart price", 'pleb');
    }

    public function getVariants(): array
    {
        return [
            'tax_exclude' => __("Tax exclusive", 'pleb'),
            'tax_include' => __("Tax inclusive", 'pleb'),
        ];
    }

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
        return 'number';
    }

}
