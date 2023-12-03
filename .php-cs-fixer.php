<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true,
        '@PHP80Migration' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'concat_space' => ['spacing' => 'none']
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
;