<?php

return PhpCsFixer\Config::create()
    ->setFinder(
        Symfony\Component\Finder\Finder::create()
            ->in(__DIR__.'/src')
            ->in(__DIR__.'/test')
    )
    ->setRules([
        '@Symfony' => true,
        'array_syntax' => ['syntax' => 'short'],
    ])
;
