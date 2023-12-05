<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;
use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumericFloat;

class RuleConditionCartPrice extends RuleConditionNumericFloat
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

    public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
    {
        $conditionComparator = $rule->getConditionComparator();
        if(is_null($conditionComparator)) {
            return false;
        }

        $conditionValue = $rule->getConditionValue();
        if(is_null($conditionValue)) {
            return false;
        }
        $conditionValue = floatval($conditionValue);

        $method = new RulesShippingMethod($methodInstanceId);
        $package_cost = ($method && $method->is_prices_include_tax()) ? $package['cart_subtotal'] : $package['contents_cost'];
        $package_cost = floatval($package_cost);

        switch($conditionComparator) {
            case '<':
                if($package_cost < $conditionValue) {
                    return true;
                }
                break;
            case '<=':
                if($package_cost <= $conditionValue) {
                    return true;
                }
                break;
            case '=':
                if($package_cost == $conditionValue) {
                    return true;
                }
                break;
            case '>=':
                if($package_cost >= $conditionValue) {
                    return true;
                }
                break;
            case '>':
                if($package_cost > $conditionValue) {
                    return true;
                }
                break;
        }

        return false;
    }

}
