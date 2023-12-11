<?php

namespace PlebWooCommerceShippingRulesets\Models;

use PlebWooCommerceShippingRulesets\RulesShippingMethod;
use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Contracts\RuleConditionInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleCondition;

class Rule implements RuleInterface
{
	private $id;
	private $condition_id = null;
	private $condition_comparator = null;
	private $condition_value = null;

	private function __construct()
	{
	}

	public static function createFromArray(array $ruleArray): self
	{
		$ruleArray = array_merge([
			'id'                   => $id = self::generateId(),
			'condition_id'         => null,
			'condition_comparator' => null,
			'condition_value'      => null,
		], $ruleArray);

		$instance = new self();
		$instance->setId($ruleArray['id']);
		$instance->setConditionId($ruleArray['condition_id']);
		$instance->setConditionComparator($ruleArray['condition_comparator']);
		$instance->setConditionValue($ruleArray['condition_value']);
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

	public function setConditionId(?string $conditionId): self
	{
		$this->condition_id = $conditionId;
		return $this;
	}

	public function getConditionId(): ?string
	{
		return $this->condition_id;
	}

	public function getCondition(): ?RuleConditionInterface
	{
		return RuleCondition::find($this->condition_id);
	}

	public function getConditionVariant(): ?string
	{
		$variant = null;

		$conditionId = $this->getConditionId();
		if ($conditionId && str_contains($conditionId, ':')) {
			$idParts = explode(':', $conditionId);
			if (count($idParts) >= 2) {
				$id = $idParts[0];
				$variant = $idParts[1];
			}
		}

		return $variant;
	}

	public function setConditionComparator(?string $conditionComparator): self
	{
		$condition = $this->getCondition();

		if ($condition && array_key_exists($conditionComparator, $condition->getComparators())) {
			$this->condition_comparator = $conditionComparator;
		} else {
			$this->condition_comparator = null;
		}

		return $this;
	}

	public function getConditionComparator(): ?string
	{
		return $this->condition_comparator;
	}

	public function setConditionValue(?string $value): self
	{
		$this->condition_value = $value;
		return $this;
	}

	public function getConditionValue(): ?string
	{
		return $this->condition_value;
	}

	public function htmlRender(string $fieldKey): string
	{
		$allRuleConditions = RuleCondition::all();
		
		ob_start();
		include(dirname(__FILE__, 2).'/Templates/RulesetsRulesetRule.php');
		return ob_get_clean();
	}

	public function matchToWooCommercePackageArray(array $package = [], int $methodInstanceId = 0): bool
	{
		$condition = $this->getCondition();

		if ($condition) {
			return $condition->matchToWooCommercePackageArray($package, $this, $methodInstanceId);
		}

		return false;
	}
}
