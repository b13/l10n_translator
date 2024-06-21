<?php
defined('TYPO3') or die();

// Module registration for TYPO3 v11 only.
// For TYPO3 v12, modules are configured in Configuration/Backend/Modules.php
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
    'L10nTranslator',
    'web',
    'translator',
    '',
    [\B13\L10nTranslator\Controller\TranslationFileController::class => 'list'],
    [
        'access' => 'user,group',
        'icon' => 'EXT:l10n_translator/Resources/Public/Icons/Extension.svg',
        'labels' => 'LLL:EXT:l10n_translator/Resources/Private/Language/locallang_translator.xlf',
        'navigationComponentId' => '',
        'inheritNavigationComponentFromMainModule' => false
    ]
);
