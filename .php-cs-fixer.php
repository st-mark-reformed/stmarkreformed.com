<?php

use PhpCsFixer\Config;

$finder = PhpCsFixer\Finder::create()
    ->exclude(['vendor'])
    ->in([
        __DIR__ . '/config',
        __DIR__ . '/src',
    ]);

return (new Config())
    ->setUsingCache(false)
    ->setRules(
        [
            'mb_str_functions' => true,
        ]
    )
    ->setRiskyAllowed(true)
    ->setFinder($finder);
