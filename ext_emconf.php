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
    'version' => '4.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '>=11.5.0',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
