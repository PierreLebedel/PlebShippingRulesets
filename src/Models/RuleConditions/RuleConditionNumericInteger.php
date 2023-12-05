<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumeric;

abstract class RuleConditionNumericInteger extends RuleConditionNumeric
{
    
    public function getType(): string
    {
        return 'numeric:integer';
    }

}
