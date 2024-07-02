<?php

namespace B13\L10nTranslator\Configuration;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class L10nConfiguration implements SingletonInterface
{
    protected array $availableL10nFiles = [];
    protected array $absolutePathsToConfiguredFiles = [];
    protected bool $supportsDefault = false;
    protected bool $allowHtmlInLabel = false;
    protected array $availableLanguages = [];

    /**
     * L10nConfiguration constructor.
     */
    public function __construct()
    {
        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableL10nFiles'])) {
            $this->availableL10nFiles = GeneralUtility::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableL10nFiles'],
                true
            );
        }

        if (!empty($GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableLanguages'])) {
            $this->availableLanguages = GeneralUtility::trimExplode(
                ',',
                $GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['availableLanguages'],
                true
            );
            $this->supportsDefault = in_array('default', $this->availableLanguages);
        }

        $this->allowHtmlInLabel = (bool)$GLOBALS['TYPO3_CONF_VARS']['EXTENSIONS']['l10n_translator']['allowHtmlInLabel'];

        if (!empty($this->availableL10nFiles) && empty($this->absolutePathsToConfiguredFiles)) {
            foreach ($this->availableL10nFiles as $availableL10nFile) {
                $this->absolutePathsToConfiguredFiles['EXT:' . $availableL10nFile] = GeneralUtility::getFileAbsFileName('EXT:' . $availableL10nFile);
            }
        }
    }

    public function getAvailableL10nFiles(): array
    {
        return $this->availableL10nFiles;
    }

    public function getAbsolutePathsToConfiguredFiles(): array
    {
        return $this->absolutePathsToConfiguredFiles;
    }

    public function supportsDefault(): bool
    {
        return $this->supportsDefault;
    }

    public function isFileAvailable(string $file): bool
    {
        return in_array($file, $this->availableL10nFiles);
    }

    public function isAbsoluteFilePathAvailable(string $file): bool
    {
        return in_array($file, $this->getAbsolutePathsToConfiguredFiles(), true);
    }

    public function getExtensionPathSyntaxForAbsolutePath(string $file): string
    {
        if (!$this->isAbsoluteFilePathAvailable($file)) {
            return '';
        }
        return array_search($file, $this->absolutePathsToConfiguredFiles);
    }

    public function getAvailableL10nLanguages(): array
    {
        return $this->availableLanguages;
    }

    public function isHtmlAllowed(): bool
    {
        return $this->allowHtmlInLabel;
    }
}
