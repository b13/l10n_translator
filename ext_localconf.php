<?php

defined('TYPO3') or die();

// XCLASS for enabling reading default language labels from l10n resp. var/labels
$GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][\TYPO3\CMS\Core\Localization\Parser\XliffParser::class] = [
    'className' => \B13\L10nTranslator\Localization\Parser\XliffParser::class
];
