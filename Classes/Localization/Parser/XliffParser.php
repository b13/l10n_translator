<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Localization\Parser;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\L10nTranslator\Configuration\L10nConfiguration;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class XliffParser
 *
 * XCLASS to support default.locallang.xlf files that are written to by
 * this extension. By default TYPO3 does not respect localizedFileNames
 * for language `default`.
 */
class XliffParser extends \TYPO3\CMS\Core\Localization\Parser\XliffParser
{
    /**
     * Returns parsed representation of XML file.
     *
     * @param string $sourcePath Source file path
     * @param string $languageKey Language key
     * @throws \TYPO3\CMS\Core\Localization\Exception\FileNotFoundException
     */
    public function _getParsedData($sourcePath, $languageKey, ?string $labelsPath): array
    {
        if (Environment::isCli()) {
            return parent::_getParsedData($sourcePath, $languageKey, $labelsPath);
        }

        if ($languageKey !== 'default') {
            return parent::_getParsedData($sourcePath, $languageKey, $labelsPath);
        }

        $l10nManagerConfiguration = GeneralUtility::makeInstance(L10nConfiguration::class);
        if ($l10nManagerConfiguration->supportsDefault() === false) {
            return parent::_getParsedData($sourcePath, $languageKey, $labelsPath);
        }

        if ($l10nManagerConfiguration->isAbsoluteFilePathAvailable($sourcePath) === false) {
            return parent::_getParsedData($sourcePath, $languageKey, $labelsPath);
        }

        $this->languageKey = $languageKey;
        $this->sourcePath = $this->getLocalizedFileName(
            str_replace(
                'EXT:',
                Environment::getExtensionsPath() . '/',
                $l10nManagerConfiguration->getExtensionPathSyntaxForAbsolutePath($sourcePath)
            ),
            $this->languageKey
        );
        // copied from parent::getParsedData from here on
        if (!@is_file($this->sourcePath)) {
            // Global localization is not available, try split localization file
            $this->sourcePath = $this->getLocalizedFileName($sourcePath, $languageKey, true);
        }
        if (!@is_file($this->sourcePath)) {
            // another change here. If we cannot find a localizedFile for default, fallback to core handling
            return parent::_getParsedData($sourcePath, $languageKey, $labelsPath);
        }
        $LOCAL_LANG = [];
        $LOCAL_LANG[$languageKey] = $this->parseXmlFile();
        return $LOCAL_LANG;
    }
}
