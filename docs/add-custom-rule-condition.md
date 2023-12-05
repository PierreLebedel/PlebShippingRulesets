[Project home](../README.md) > [Docs home](index.md)

# Create your custom rule condition

## Declare your PHP class

Your class must implements ``\PlebWooCommerceShippingRulesets\Contracts\RuleConditionInterface``

You can extends ``\PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleCondition``

```php
use \PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleCondition;
use \PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use \PlebWooCommerceShippingRulesets\RulesShippingMethod;

class CustomRuleCondition extends RuleCondition
{
	public function getId(): string
	{
		return 'custom';
	}

	public function getName(): string
	{
		return __("Custom rule");
	}

	public function getVariants(): array
	{
		return [
			'sub_rule_1' => __("Sub rule #1"),
			'sub_rule_2' => __("Sub rule #2"),
		];
	}

	public function getComparators(): array
	{
		return [
			'=',
			'!='
		];
	}

	public function getInputHtml(string $fieldName, mixed $value): string
	{
		ob_start();
		?><input type="text" name="<?php echo esc_attr($fieldName); ?>" value="<?php esc_attr_e($value); ?>" class="w-100" required><?php
		return ob_get_clean();
	}

	public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
	{
		$conditionComparator = $rule->getConditionComparator();
		if(is_null($conditionComparator)) return false;

		$conditionValue = $rule->getConditionValue();
		if(is_null($conditionValue)) return false;

		// if needed, you can load the \WC_Shipping_Method instance (to get options value or public properties)
		// $shipping_method = new RulesShippingMethod($methodInstanceId);
		// $package_cost = ($shipping_method && $shipping_method->is_prices_include_tax()) ? $package['cart_subtotal'] : $package['contents_cost'];

		// Check your condition
		if(true){
			return true;
		}

		return false;
	}
}
```

## Add to available rule conditions

In your plugin or in your theme's ``functions.php``:

```php
add_filter('plebwcsr_rule_condition_all', function(array $ruleConditions = []){
	$ruleConditions[] = CustomRuleCondition::class;
	return $ruleConditions;
});
```

Your own rule is now available in the shipping by rulesets settings pages.