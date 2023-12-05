<?php

namespace PlebWooCommerceShippingRulesets\Models\RuleConditions;

use PlebWooCommerceShippingRulesets\Models\RuleConditions\RuleConditionNumeric;

abstract class RuleConditionNumericFloat extends RuleConditionNumeric
{
    
    public function getType(): string
    {
        return 'numeric:float';
    }

}
