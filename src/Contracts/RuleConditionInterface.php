<?php

namespace PlebWooCommerceShippingRulesets\Contracts;

interface RuleConditionInterface
{

    public function getId(): string;

    public function getName(): string;
    
    public function getComparators(): array;

    public function getType(): string;

}