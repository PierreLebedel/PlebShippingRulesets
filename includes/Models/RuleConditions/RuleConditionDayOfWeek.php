<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Contracts\RuleInterface;
use PlebWooCommerceShippingRulesets\Contracts\RuleConditionsGroupInterface;
use PlebWooCommerceShippingRulesets\Models\RuleConditionsGroups\DateTimeGroup;
use PlebWooCommerceShippingRulesets\Models\RuleConditions\Abstracts\RuleConditionChoices;

class RuleConditionDayOfWeek extends RuleConditionChoices
{
	public function getId(): string
	{
		return 'day_of_week';
	}

	public function getName(): string
	{
		return __("Day of week", 'pleb-woocommerce-shipping-rulesets');
	}

	public function getGroup(): ?RuleConditionsGroupInterface
	{
		return new DateTimeGroup();
	}

	public function getComparators(): array
	{
		return [
			'='  => "=",
			'!=' => "!=",
		];
	}

	public function getChoices(): array
	{
		/**
		 * @global \WP_Locale $wp_locale WordPress date and time locale object.
		 */
		global $wp_locale;
		
		$days = [];

		$startIndex = get_option( 'start_of_week', 0 );

		// on boucle du premier jour de la semaine (réglages) au samedi
		for($i=$startIndex; $i<7; $i++){
			$days[$i] = ucfirst($wp_locale->get_weekday( $i ));
		}

		// on boucle du dimanche à l'avant-premier jour de la semaine
		for($i=0; $i<$startIndex; $i++){
			$days[$i] = ucfirst($wp_locale->get_weekday( $i ));
		}

		return $days;
	}

	public function matchToWooCommercePackageArray(array $package = [], ?RuleInterface $rule = null, int $methodInstanceId = 0): bool
	{
		$conditionComparator = $rule->getConditionComparator();
		if (is_null($conditionComparator)) {
			return false;
		}
		
		$conditionValue = $rule->getConditionValue();
		if (is_null($conditionValue)) {
			return false;
		}
		$conditionValue = intval($conditionValue);

		if( $conditionComparator=='=' && $conditionValue==intval(wp_date('w')) ){
			return true;
		}

		if( $conditionComparator=='!=' && $conditionValue!=intval(wp_date('w')) ){
			return true;
		}

		return false;
	}
}
