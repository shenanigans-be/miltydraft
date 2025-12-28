<?php

declare(strict_types=1);

use PhpCsFixer\Config;
use PhpCsFixer\Runner\Parallel\ParallelConfigFactory;
use Symfony\Component\Finder\Finder;

$finder = Finder::create()
    ->in([
        __DIR__ . '/app',
    ])
    ->name('*.php')
    ->ignoreDotFiles(true)
    ->ignoreVCS(true);

return (new Config())
    ->setParallelConfig(ParallelConfigFactory::detect())
    ->setRiskyAllowed(true)
    ->setRules([
        'array_syntax' => ['syntax' => 'short'],
        'ordered_imports' => [
            'imports_order' => ['class', 'function', 'const'],
            'sort_algorithm' => 'alpha',
        ],
        'fully_qualified_strict_types' => [
            'phpdoc_tags' => [],
        ],
        'no_unused_imports' => true,
        'blank_line_after_opening_tag' => true,
        'declare_strict_types' => true,
        'not_operator_with_successor_space' => true,
        'trailing_comma_in_multiline' => [
            'elements' => ['arguments', 'arrays', 'match', 'parameters'],
        ],
        'phpdoc_scalar' => true,
        'unary_operator_spaces' => true,
        'binary_operator_spaces' => true,
        'blank_line_before_statement' => [
            'statements' => ['break', 'continue', 'declare', 'return', 'throw', 'try'],
        ],
        'no_extra_blank_lines' => [
            'tokens' => ['parenthesis_brace_block', 'return', 'square_brace_block', 'extra'],
        ],
        'phpdoc_single_line_var_spacing' => true,
        'phpdoc_var_without_name' => true,
        'method_argument_space' => [
            'on_multiline' => 'ensure_fully_multiline',
            'keep_multiple_spaces_after_comma' => true,
        ],
        'void_return' => true,
        'single_quote' => true,
        'multiline_promoted_properties' => [
            'minimum_number_of_parameters' => 1,
        ],
    ])
    ->setFinder($finder);
