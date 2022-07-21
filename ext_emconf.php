<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'l10n Translator',
    'description' => 'translate files in var/labels folder',
    'category' => 'module',
    'author' => 'Achim Fritz, Daniel Goerz, Michael Giek',
    'author_email' => 'af@b13.com',
    'state' => 'stable',
    'internal' => '',
    'uploadfolder' => '0',
    'createDirs' => '',
    'clearCacheOnLoad' => 0,
    'version' => '3.1.0',
    'constraints' => [
        'depends' => [
            'typo3' => '>=10.4.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
