<?php

$finder = PhpCsFixer\Finder::create()
    ->in('src')
    ->in('tests');

$fixer = new PhpCsFixer\Config();

$fixer->setRiskyAllowed(true)
    ->setRules(
        [
            'array_syntax'           => ['syntax' => 'short'],
            '@PSR2'                  => true,
            '@Symfony'               => true,
            'binary_operator_spaces' => ['align_equals' => false, 'align_double_arrow' => true],
        ]
    )
    ->setFinder($finder);

return $fixer;
