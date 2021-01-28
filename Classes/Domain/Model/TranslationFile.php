<?php
namespace B13\L10nTranslator\Domain\Model;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Localization\LocalizationFactory;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFile extends AbstractTranslationFile
{
    /**
     * @var L10nTranslationFile[]
     */
    protected $l10nTranslationFiles = [];

    /**
     * @param \SplFileInfo $splFileInfo
     * @param array $languages
     * @param LocalizationFactory $localizationFactory
     * @throws Exception
     */
    public function initFileSystem(\SplFileInfo $splFileInfo, array $languages, LocalizationFactory $localizationFactory): void
    {
        $this->splFileInfo = $splFileInfo;

        $pathPart = str_replace('/', '\/', Environment::getExtensionsPath() . DIRECTORY_SEPARATOR);
        $this->relativePath = preg_replace('/' . $pathPart . '/', '', $this->getCleanPath());
        $parts = explode(DIRECTORY_SEPARATOR, $this->relativePath);
        if (empty($parts)) {
            throw new Exception('Invalid file in ' . $this->splFileInfo->getRealPath(), 1466171558);
        }
        $this->language = 'default';
        $this->extension = $parts[0];
        $this->initTranslations($localizationFactory);
        foreach ($languages as $language) {
            $path = $this->getL10nTranslationFilePath($language);
            $splFileInfo = new \SplFileInfo($path);
            $l10nTranslationFile = new L10nTranslationFile($this);
            $l10nTranslationFile->initFileSystem($splFileInfo, $localizationFactory);
            $l10nTranslationFile->initMissingTranslations();
            $this->l10nTranslationFiles[$language] = $l10nTranslationFile;
        }
    }

    /**
     * @param LocalizationFactory $localizationFactory
     * @return array
     */
    protected function getParsedData(LocalizationFactory $localizationFactory): array
    {
        return $localizationFactory->getParsedData($this->getCleanPath(), $this->getLanguage());
    }

    /**
     * @param string $language
     * @throws Exception
     */
    public function getL10nTranslationFile(string $language): L10nTranslationFile
    {
        if (isset($this->l10nTranslationFiles[$language]) === false) {
            throw new Exception('l10nTranslationFile of language ' . $language . ' does not exist.', 1466587863);
        }
        return $this->l10nTranslationFiles[$language];
    }

    /**
     * @param string $language
     */
    public function getL10nTranslationFilePath(string $language): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, $this->getRelativePath());
        array_pop($parts);
        $path = Environment::getLabelsPath() . DIRECTORY_SEPARATOR . $language;
        $path .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $language . '.' . $this->getSplFileInfo()->getBasename();
        return $path;
    }

    /**
     * @param Search $search
     */
    public function applySearch(Search $search): void
    {
        foreach ($this->getL10nTranslationFiles() as $l10nTranslationFile) {
            $l10nTranslationFile->applySearch($search);
        }
    }

    /**
     * @return L10nTranslationFile[]
     */
    public function getL10nTranslationFiles(): array
    {
        return $this->l10nTranslationFiles;
    }
}
