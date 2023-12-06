<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__)
    ->exclude([
        'vendor',
        'svn'
    ])
;

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR1' => true,
        '@PSR2' => true,
        '@PSR12' => true,
        '@PHP80Migration' => true,
        'strict_param' => true,
        'array_syntax' => ['syntax' => 'short'],
        'trim_array_spaces' => true,
        'whitespace_after_comma_in_array' => true,
        'concat_space' => ['spacing' => 'none'],
        'method_argument_space' => [
            'keep_multiple_spaces_after_comma' => false,
            'on_multiline' => 'ensure_fully_multiline', 
            'keep_multiple_spaces_after_comma' => false,
            'attribute_placement' => 'ignore',
            'attribute_placement' => 'same_line',
            'after_heredoc' => true
        ],
        'statement_indentation' => false,
        'indentation_type' => false,
        'line_ending' => true
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder)
    ->setIndent("\t")
    ->setLineEnding("\r\n")
;