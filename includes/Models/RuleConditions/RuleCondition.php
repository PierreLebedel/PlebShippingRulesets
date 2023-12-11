<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Contracts\RuleConditionInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionCartPrice;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionCartItemCount;

abstract class RuleCondition implements RuleConditionInterface
{
	public static function all(): array
	{
		$classes = apply_filters('plebwcsr_rule_condition_all', [
			RuleConditionCartPrice::class,
			RuleConditionCartWeight::class,
			RuleConditionCartItemCount::class,
			RuleConditionCartItemByShippingClassCount::class,
			RuleConditionCartItemByCategoryCount::class,
			RuleConditionUserIsLoggedIn::class,
		]);

		$conditions = [];
		foreach (array_unique($classes) as $class) {
			if (!class_exists($class)) {
				continue;
			}

			$instance = new $class();

			if (!$instance instanceof RuleConditionInterface) {
				continue;
			}

			$conditions[ $instance->getId() ] = $instance;
		}

		return $conditions;
	}

	public static function find(?string $id = null): ?self
	{
		if (is_null($id)) {
			return null;
		}

		$all = self::all();

		if (str_contains($id, ':')) {
			$idParts = explode(':', $id);
			if (count($idParts) >= 2) {
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
		return [];
	}

	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><input type="hidden" name="<?php echo esc_attr($fieldName); ?>" value="" /><?php
		return ob_get_clean();
	}

	/*public function extractValueFromWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): mixed
	{
		return null;
	}*/

	public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
	{
		// $method = new RulesShippingMethod($methodInstanceId);
		// $compareValue = $this->extractValueFromWooCommercePackageArray($package, $rule, $methodInstanceId);
		return false;
	}
}
