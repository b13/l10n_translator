<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Domain\Service;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\L10nTranslator\Configuration\L10nConfiguration;
use B13\L10nTranslator\Domain\Factory\TranslationFileFactory;
use B13\L10nTranslator\Domain\Model\L10nTranslationFile;
use B13\L10nTranslator\Domain\Model\Search;
use B13\L10nTranslator\Domain\Model\Translation;
use TYPO3\CMS\Core\SingletonInterface;

class TranslationFileService implements SingletonInterface
{
    protected TranslationFileFactory $translationFileFactory;
    protected TranslationFileWriterService $translationFileWriterService;
    protected L10nConfiguration $l10nConfiguration;

    public function __construct(
        L10nConfiguration $l10nConfiguration,
        TranslationFileFactory $translationFileFactory,
        TranslationFileWriterService $translationFileWriterService
    ) {
        $this->l10nConfiguration = $l10nConfiguration;
        $this->translationFileFactory = $translationFileFactory;
        $this->translationFileWriterService = $translationFileWriterService;
    }

    public function createMissingFiles(string $language, bool $copyLabels = true): void
    {
        $search = new Search('', '', '');
        $translationFiles = $this->translationFileFactory->findBySearch($search);
        foreach ($translationFiles as $translationFile) {
            $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
            if ($l10nTranslationFile->getSplFileInfo()->isFile() === false) {
                if ($copyLabels === true) {
                    foreach ($translationFile->getTranslations() as $translation) {
                        if ($l10nTranslationFile->hasOwnTranslation($translation)) {
                            $l10nTranslationFile->getOwnTranslation($translation)->replaceTranslationSourceByOtherTranslation($translation);
                            continue;
                        }
                        $l10nTranslationFile->addTranslation($translation);
                    }
                }
                $this->translationFileWriterService->writeTranslationXlf($l10nTranslationFile);
            }
        }
    }

    public function overwriteWithLanguage(string $l10nFile, string $language, string $sourceLanguage): void
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        $sourceL10nTranslationFile = $translationFile->getL10nTranslationFile($sourceLanguage);
        foreach ($sourceL10nTranslationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                $l10nTranslationFile->addTranslation($translation);
            } else {
                $l10nTranslationFile->replaceTranslationTarget($translation);
            }
        }
        $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
    }

    public function createMissingLabels(string $l10nFile, string $language, string $sourceLanguage = 'default'): void
    {
        if ($sourceLanguage !== 'default') {
            $l10nTranslationFile = $this->mergeMissingLabelsFromSourceLanguage($l10nFile, $language, $sourceLanguage);
        } else {
            $l10nTranslationFile = $this->mergeMissingLabelsFromDefaultLanguage($l10nFile, $language);
        }
        $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
    }

    public function removeObsoleteLabels(string $l10nFile): void
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile, true);
        foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
            $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
        }
    }

    public function createMissingSourceTags(string $l10nFile, string $language, string $sourceLanguage = 'default'): void
    {
        $l10nTranslationFile = $this->mergeSourceTagFromDefaultLanguage($l10nFile, $language);
        $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
    }

    public function createAllMissingLabels(string $language, string $sourceLanguage = 'default'): void
    {
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        foreach ($l10nFiles as $l10nFile) {
            $this->createMissingLabels($l10nFile, $language, $sourceLanguage);
        }
    }

    public function removeAllObsoleteLabels(): void
    {
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        foreach ($l10nFiles as $l10nFile) {
            $this->removeObsoleteLabels($l10nFile);
        }
    }

    public function createSourceTagsForAllFiles(string $language): void
    {
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        foreach ($l10nFiles as $l10nFile) {
            $this->createMissingSourceTags($l10nFile, $language);
        }
    }

    public function createSourceTagsForAllFilesAndLanguages(): void
    {
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        foreach ($languages as $language) {
            $this->createSourceTagsForAllFiles($language);
        }
    }

    protected function mergeMissingLabelsFromSourceLanguage(string $l10nFile, string $language, string $sourceLanguage): L10nTranslationFile
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        $sourceL10nTranslationFile = $translationFile->getL10nTranslationFile($sourceLanguage);
        foreach ($translationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                $sourceTranslation = $sourceL10nTranslationFile->getOwnTranslation($translation);
                if ($sourceTranslation !== null) {
                    $l10nTranslationFile->addTranslation($sourceTranslation);
                } else {
                    $l10nTranslationFile->addTranslation($translation);
                }
            }
        }
        return $l10nTranslationFile;
    }

    protected function mergeMissingLabelsFromDefaultLanguage(string $l10nFile, string $language): L10nTranslationFile
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        $l10nTranslationFile->fillMissingTranslationsFromOriginalFileAndLanguage($language);
        foreach ($translationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                $l10nTranslationFile->addTranslation($translation);
            }
            $currentTranslation = $l10nTranslationFile->getOwnTranslation($translation);
            if (empty($currentTranslation->getTranslationSource())) {
                $currentTranslation->replaceTranslationSourceByOtherTranslation($translation);
            }
        }
        return $l10nTranslationFile;
    }

    protected function mergeSourceTagFromDefaultLanguage(string $l10nFile, string $language): L10nTranslationFile
    {
        $translationFile = $this->translationFileFactory->findByPath($l10nFile);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
        foreach ($translationFile->getTranslations() as $translation) {
            if ($l10nTranslationFile->hasOwnTranslation($translation)) {
                $l10nTranslationFile->replaceTranslationSource($translation);
            }
        }
        return $l10nTranslationFile;
    }

    /**
     * For all configured languages we update the source of the label from the POST request.
     * This is because it was changed in the default language which is the source of all
     * other languages.
     *
     * So changes in "default" are reflected in an updated source of all other languages.
     */
    public function updateSourceInFiles(array $postParam): void
    {
        $configuredLanguages = $this->l10nConfiguration->getAvailableL10nLanguages();
        $translationFile = $this->translationFileFactory->findByPath($postParam['path']);
        $translation = new Translation('', $postParam['key'], '', $postParam['target']);

        foreach ($configuredLanguages as $language) {
            if ($language === 'default') {
                continue;
            }

            $l10nTranslationFile = $translationFile->getL10nTranslationFile($language);
            $l10nTranslationFile->replaceTranslationSource($translation);
            $this->translationFileWriterService->writeTranslationXlf($l10nTranslationFile);
        }
    }
}
