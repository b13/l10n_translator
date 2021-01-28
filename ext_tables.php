<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

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
        'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        'inheritNavigationComponentFromMainModule' => false
    ]
);
