<?php

namespace PlebShippingRulesets\Models;

use PlebShippingRulesets\Models\Rule;
use PlebShippingRulesets\Contracts\RulesetInterface;

class Ruleset implements RulesetInterface
{
	private $id;
	protected $name;
	protected $cost = '';
	protected $order = null;
	protected $rules = [];

	private function __construct()
	{
	}

	public static function createFromArray(array $rulesetArray = []): self
	{
		$rulesetArray = array_merge([
			'id'      => $id = self::generateId(),
			'name'    => __("Ruleset", 'pleb-shipping-rulesets'),
			'cost'    => '',
			'order'   => null,
			'rules'   => [],
		], $rulesetArray);

		$instance = new static();
		$instance->setId($rulesetArray['id']);
		$instance->setName($rulesetArray['name']);
		$instance->setCost($rulesetArray['cost']);
		$instance->setOrder($rulesetArray['order']);

		if (isset($rulesetArray['rules']) && is_array($rulesetArray['rules'])) {
			foreach ($rulesetArray['rules'] as $ruleArray) {
				$rule = Rule::createFromArray($ruleArray);
				$instance->addRule($rule);
			}
		}

		return $instance;
	}

	public static function generateId(): string
	{
		return uniqid();
	}

	public function setId(string $id): self
	{
		$this->id = $id;
		return $this;
	}

	public function getId(): string
	{
		if (empty($this->id)) {
			return self::generateId();
		}
		return $this->id;
	}

	public function setName(string $name): self
	{
		$this->name = $name;
		return $this;
	}

	public function getName(): string
	{
		if (empty($this->name)) {
			return __("Ruleset", 'pleb-shipping-rulesets').' #'.$this->getId();
		}
		return $this->name;
	}

	public function setCost(string $cost): self
	{
		$this->cost = $cost;
		return $this;
	}

	public function getCost(): string
	{
		return $this->cost;
	}

	public function setOrder(mixed $order): self
	{
		if (!$order == 'default' && !is_null($order)) {
			$this->order = intval($order);
		} else {
			$this->order = $order;
		}
		return $this;
	}

	public function getOrder(): mixed
	{
		return $this->order;
	}

	public function addRule(Rule $rule): self
	{
		$this->rules[] = $rule;
		return $this;
	}

	public function getRules(): array
	{
		return $this->rules;
	}

	public function isDefault(): bool
	{
		return $this->getOrder() == 'default';
	}

	public function htmlRender(string $fieldKey): string
	{
		ob_start();
		include(dirname(__FILE__, 2).'/Templates/RulesetsRuleset.php');
		return ob_get_clean();
	}

	public function matchToWooCommercePackageArray(array $package = [], int $methodInstanceId = 0): bool
	{
		$rules = $this->getRules();

		if (empty($rules)) {
			return false;
		}

		$allRulesSuccess = true;
		foreach ($rules as $rule) {
			$ruleSuccess = $rule->matchToWooCommercePackageArray($package, $methodInstanceId);
			if (!$ruleSuccess) {
				$allRulesSuccess = false;
			}
		}

		return $allRulesSuccess;
	}
}
