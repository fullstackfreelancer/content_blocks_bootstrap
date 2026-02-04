<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Content Blocks Bootstrap',
    'description' => 'Provides Content Blocks for the Bootstrap Framework',
    'category' => 'templates',
    'author' => 'Simon Köhler',
    'author_email' => 'simon@kohlercode.com',
    'state' => 'alpha',
    'clearCacheOnLoad' => 1,
    'version' => '0.0.3',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-14.1.99',
            'content_blocks' => '1.3.3 - 2.0.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'autoload' => [
        'psr-4' => [
            'KOHLERCODE\\ContentBlocksBootstrap\\' => 'Classes',
        ],
    ],
];
