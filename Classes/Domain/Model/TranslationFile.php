<?php

declare(strict_types=1);

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
use TYPO3\CMS\Core\Package\PackageManager;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TranslationFile extends AbstractTranslationFile
{
    /** @var L10nTranslationFile[] */
    protected array $l10nTranslationFiles = [];

    public function initFileSystem(\SplFileInfo $splFileInfo, array $languages, LocalizationFactory $localizationFactory): void
    {
        /** @var PackageManager $packageManager */
        $packageManager = GeneralUtility::makeInstance(PackageManager::class);
        $this->splFileInfo = $splFileInfo;
        // Escape directory separators in path to vendor directory
        $pathPart = str_replace('/', '\/', Environment::getProjectPath() . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR);
        // Remove the path to the vendor directory from the path to the file
        $this->relativePath = preg_replace('/' . $pathPart . '/', '', $this->getCleanPath());
        // Find the vendor/package-name used by composer
        $matches = [];
        preg_match('#^[^\/]*\/[^\/]*#', $this->relativePath, $matches);
        // Convert the composer package name into the TYPO3 extension name.
        // Append the extension name instead of the composer package name to the relative path.
        // Example: Instead of "vendor-name/my-extension/Resources/Private/" we will get "my_extension/Resources/Private/",
        // provided the package has configured "my_extension" as the TYPO3 extension key in composer.json.
        $this->relativePath = $packageManager->getPackageKeyFromComposerName($matches[0]) . preg_replace('#(^[^\/]*\/[^\/]*)#', '', $this->relativePath);

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

    protected function getParsedData(LocalizationFactory $localizationFactory): array
    {
        return $localizationFactory->getParsedData($this->getCleanPath(), $this->getLanguage());
    }

    public function getL10nTranslationFile(string $language): L10nTranslationFile
    {
        if (isset($this->l10nTranslationFiles[$language]) === false) {
            throw new Exception('l10nTranslationFile of language ' . $language . ' does not exist.', 1466587863);
        }
        return $this->l10nTranslationFiles[$language];
    }

    public function getL10nTranslationFilePath(string $language): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, $this->getRelativePath());
        array_pop($parts);
        $path = Environment::getLabelsPath() . DIRECTORY_SEPARATOR . $language;
        // There is no way from the composer package name without the vendor to the extension key.
        // Replacing "-" with "_" is a close enough approximation.
        $parts[0] = str_replace('-', '_', $parts[0]);
        $path .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $parts) . DIRECTORY_SEPARATOR . $language . '.' . $this->getSplFileInfo()->getBasename();
        return $path;
    }

    public function applySearch(Search $search): void
    {
        foreach ($this->getL10nTranslationFiles() as $l10nTranslationFile) {
            $l10nTranslationFile->applySearch($search);
        }
    }

    /** @return L10nTranslationFile[] */
    public function getL10nTranslationFiles(): array
    {
        return $this->l10nTranslationFiles;
    }
}
