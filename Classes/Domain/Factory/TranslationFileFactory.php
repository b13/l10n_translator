<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Domain\Factory;

use B13\L10nTranslator\Configuration\L10nConfiguration;
use B13\L10nTranslator\Domain\Model\RawTranslationFile;
use B13\L10nTranslator\Domain\Model\Search;
/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
use B13\L10nTranslator\Domain\Model\TranslationFile;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

readonly class TranslationFileFactory implements SingletonInterface
{
    public function __construct(
        protected L10nConfiguration $l10nConfiguration,
        protected XliffParser $xliffParser
    ) {}

    public function findByRelativePath(string $relativePath, bool $raw = false): TranslationFile
    {
        $splFileInfo = new \SplFileInfo(GeneralUtility::getFileAbsFileName('EXT:' . $relativePath));
        if ($splFileInfo->isFile() === false) {
            throw new Exception('Cannot create splFileInfo with path ' . $relativePath, 1466093531);
        }
        $translationFile = $raw ? new RawTranslationFile() : new TranslationFile();
        $translationFile->initFileSystem($splFileInfo, $this->l10nConfiguration->getAvailableL10nLanguages(), $this->xliffParser);
        return $translationFile;
    }

    public function findByPath(string $path, bool $raw = false): TranslationFile
    {
        try {
            $translationFile = $this->findByRelativePath($path, $raw);
        } catch (Exception $e) {
            $splFileInfo = new \SplFileInfo($path);
            if ($splFileInfo->isFile() === false) {
                throw new Exception('cannot create splFileInfo with path ' . $path, 1466093537);
            }
            $translationFile = $raw ? new RawTranslationFile() : new TranslationFile();
            $translationFile->initFileSystem($splFileInfo, $this->l10nConfiguration->getAvailableL10nLanguages(), $this->xliffParser);
        }
        return $translationFile;
    }

    /**
     * @param Search $search
     * @return TranslationFile[]
     * @throws \B13\L10nTranslator\Domain\Model\Exception
     */
    public function findBySearch(Search $search): array
    {
        $translationFiles = [];
        $languages = $search->hasLanguage() ? [$search->getLanguage()] : $this->l10nConfiguration->getAvailableL10nLanguages();
        if ($search->hasL10nFile() && !in_array($search->getL10nFile(), $this->l10nConfiguration->getAvailableL10nFiles(), true)) {
            throw new Exception('File not configured: ' . $search->getL10nFile(), 1466093540);
        }
        $availableL10nFiles = $search->hasL10nFile() ? [$search->getL10nFile()] : $this->l10nConfiguration->getAvailableL10nFiles();
        foreach ($availableL10nFiles as $availableL10nFile) {
            $path = GeneralUtility::getFileAbsFileName('EXT:' . $availableL10nFile);
            if ($path === '') {
                continue;
            }
            $translationFile = new TranslationFile();
            $translationFile->initFileSystem(new \SplFileInfo($path), $languages, $this->xliffParser);
            $translationFile->applySearch($search);
            $translationFiles[] = $translationFile;
        }
        return $translationFiles;
    }
}
