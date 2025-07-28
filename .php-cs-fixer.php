<?php

return (new PhpCsFixer\Config())
    ->setFinder(
        Symfony\Component\Finder\Finder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/test')
    )
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
        // Needed for PHP 7 compatibility
        'trailing_comma_in_multiline' => false,
        'global_namespace_import' => [
            'import_classes' => true,
        ],
    ])
;
