<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;
use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Contracts\RuleConditionInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionCartPrice;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionCartItemCount;

abstract class RuleCondition implements RuleConditionInterface
{
    public static function all(): array
    {
        $classes = [
            RuleConditionCartItemCount::class,
            RuleConditionCartPrice::class,
        ];

        $conditions = [];
        foreach($classes as $class) {
            $instance = new $class();
            $conditions[ $instance->getId() ] = $instance;
        }

        return $conditions;
    }

    public static function find(?string $id = null): ?self
    {
        if(is_null($id)) {
            return null;
        }

        $all = self::all();

        if(str_contains($id, ':')) {
            $idParts = explode(':', $id);
            if(count($idParts) >= 2) {
                $id = $idParts[0];
                $variant = $idParts[1];
            }
        }

        return $all[$id] ?? null;
    }

    public function getVariants(): array
    {
        return [];
    }

    public function getComparators(): array
    {
        return [
            '=',
        ];
    }

    public function getType(): string
    {
        return 'none';
    }

    public function matchToWooCommercePackageArray(RuleInterface $rule, array $package = [], ?RulesShippingMethod $method = null): bool
    {
        return false;
    }

}
