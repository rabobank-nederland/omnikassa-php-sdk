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
        'global_namespace_import' => [
            'import_classes' => true,
        ],
    ])
;
