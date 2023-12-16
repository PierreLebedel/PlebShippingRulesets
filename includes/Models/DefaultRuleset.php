<?php

namespace PlebShippingRulesets\Models;

class DefaultRuleset extends Ruleset
{
	protected $order = 'default';

	public function htmlRender(string $fieldKey): string
	{
		ob_start();
        include(dirname(__FILE__, 2).'/Templates/RulesetsDefaultRuleset.php');
        return ob_get_clean();
	}

	public function matchToWooCommercePackageArray(array $package = [], int $methodInstanceId = 0): bool
	{
		return true;
	}
}
