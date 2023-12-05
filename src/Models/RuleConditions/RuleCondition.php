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
        $classes = apply_filters('plebwcsr_rule_condition_all', [
            RuleConditionCartItemCount::class,
            RuleConditionCartPrice::class,
        ]);

        $conditions = [];
        foreach($classes as $class) {
            if(!class_exists($class)) continue;

            $instance = new $class();

            if(!$instance instanceof RuleConditionInterface) continue;

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

    public function getInputHtml(string $fieldName, mixed $value): string
    {
        ob_start();
		?><input type="hidden" name="<?php echo esc_attr($fieldName); ?>" value="" /><?php
		return ob_get_clean();
    }

    public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
    {
        return false;
    }

}
