<?php

namespace PlebWooCommerceShippingRulesets\Models;

use PlebWooCommerceShippingRulesets\Contracts\RuleConditionInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionCartItemCount;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionCartPrice;

abstract class RuleCondition implements RuleConditionInterface
{

    public static function all(): array
    {
        $classes = [
            RuleConditionCartItemCount::class,
            RuleConditionCartPrice::class,
        ];

        $conditions = [];
        foreach($classes as $class){
            $instance = new $class();
            $conditions[ $instance->getId() ] = $instance;
        }

        return $conditions;
    }

    public static function find(?string $id = null): ?self
    {
        if(is_null($id)) return null;

        $all = self::all();

        if( str_contains($id, ':') ){
            $idParts = explode(':', $id);
            if( count($idParts)>=2 ){
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

}