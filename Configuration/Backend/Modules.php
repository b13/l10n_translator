<?php

declare(strict_types=1);

return [
    'web_B13L10ntranslator' => [
        'parent' => 'web',
        'position' => [],
        'access' => 'user,group',
        'path' => '/module/web/l10ntranslator',
        'workspaces' => 'live',
        'iconIdentifier' => 'b13_l10ntranslator',
        'navigationComponentId' => '',
        'inheritNavigationComponentFromMainModule' => false,
        'labels' => 'LLL:EXT:l10n_translator/Resources/Private/Language/locallang_translator.xlf',
        'extensionName' => 'L10nTranslator',
        'controllerActions' => [
            \B13\L10nTranslator\Controller\TranslationFileController::class => 'list',
        ],
    ],
];
