<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Content Blocks Bootstrap',
    'description' => 'Provides Content Blocks for the Bootstrap Framework',
    'category' => 'templates',
    'author' => 'Simon Köhler',
    'author_email' => 'simon@kohlercode.com',
    'state' => 'beta',
    'clearCacheOnLoad' => 1,
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.4.0-13.9.99',
            'content_blocks' => '1.3.3'
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
