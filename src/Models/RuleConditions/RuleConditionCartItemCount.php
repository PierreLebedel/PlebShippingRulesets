<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;
use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumericInteger;

class RuleConditionCartItemCount extends RuleConditionNumericInteger
{
    public function getId(): string
    {
        return 'cart_item_count';
    }

    public function getName(): string
    {
        return __("Cart item quantity", 'pleb');
    }

    public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
    {
        $conditionComparator = $rule->getConditionComparator();
        if(is_null($conditionComparator)) return false;

        $conditionValue = $rule->getConditionValue();
        if(is_null($conditionValue)) return false;
        $conditionValue = intval($conditionValue);

        $package_quantity = 0;
        foreach ($package['contents'] as $values) {
            if ($values['quantity'] > 0 && $values['data']->needs_shipping()) {
                $package_quantity += intval($values['quantity']);
            }
        }

        switch($conditionComparator){
            case '<':
                if($package_quantity < $conditionValue) return true;
                break;
            case '<=':
                if($package_quantity <= $conditionValue) return true;
                break;
            case '=':
                if($package_quantity == $conditionValue) return true;
                break;
            case '>=':
                if($package_quantity >= $conditionValue) return true;
                break;
            case '>':
                if($package_quantity > $conditionValue) return true;
                break;
        }

        return false;
    }

}
