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
use TYPO3\CMS\Core\Localization\Exception\FileNotFoundException;
use TYPO3\CMS\Core\Localization\Parser\XliffParser;

class L10nTranslationFile extends AbstractTranslationFile
{
    /** @var Translation[] */
    protected array $missingTranslations = [];

    /** @var Translation[] */
    protected array $matchedMissingTranslations = [];
    protected ?XliffParser $xliffParser = null;

    public function __construct(protected TranslationFile $translationFile)
    {
    }

    public function initFileSystem(\SplFileInfo $splFileInfo, XliffParser $xliffParser): void
    {
        $this->xliffParser = $xliffParser;
        $this->splFileInfo = $splFileInfo;
        $pathPart = str_replace('/', '\/', Environment::getLabelsPath() . DIRECTORY_SEPARATOR);
        $this->relativePath = preg_replace('/' . $pathPart . '/', '', $this->getCleanPath());
        $parts = explode(DIRECTORY_SEPARATOR, $this->relativePath);
        if (count($parts) < 2) {
            throw new Exception('Invalid file in ' . $this->splFileInfo->getRealPath(), 1466171553);
        }
        $this->language = $parts[0];
        $this->extension = $parts[1];
        $this->initTranslations($xliffParser);
    }

    public function fillMissingTranslationsFromOriginalFileAndLanguage(string $language): void
    {
        if ($this->xliffParser === null) {
            return;
        }
        try {
            $parsedData = $this->xliffParser->getParsedData($this->getTranslationFile()->getCleanPath(), $language);
        } catch (FileNotFoundException) {
            return;
        }
        foreach ($parsedData[$language] as $key => $labels) {
            if (!isset($labels[0]['source']) || !isset($labels[0]['target'])) {
                continue;
            }
            $translation = new Translation($this->getCleanPath(), $key, $labels[0]['target'], $labels[0]['source']);
            if ($this->hasOwnTranslation($translation)) {
                continue;
            }
            $this->addTranslation($translation);
        }
    }

    protected function getParsedData(XliffParser $xliffParser): array
    {
        try {
            if ($this->getSplFileInfo()->isFile() === true) {
                return $xliffParser->getParsedData($this->getCleanPath(), $this->getLanguage());
            }
            return $xliffParser->getParsedData($this->getTranslationFile()->getCleanPath(), $this->getLanguage());
        } catch (FileNotFoundException) {
            // No file for this language yet - treat as empty
            return [$this->getLanguage() => []];
        }
    }

    public function initMissingTranslations(): void
    {
        foreach ($this->translationFile->getTranslations() as $translation) {
            if ($this->hasOwnTranslation($translation) === false) {
                $this->missingTranslations[] = new Translation($translation->getPath(), $translation->getTranslationKey(), '', $translation->getTranslationSource());
            }
        }
    }

    public function removeObsoleteTranslations(): void
    {
        $translationsToKeep = [];
        foreach ($this->translations as $translation) {
            if ($this->translationFile->hasOwnTranslation($translation)) {
                $translationsToKeep[] = $translation;
            }
        }
        $this->translations = $translationsToKeep;
    }

    /** @return Translation[] */
    public function getMissingTranslations(): array
    {
        return $this->missingTranslations;
    }

    /** @return Translation[] */
    public function getMatchedMissingTranslations(): array
    {
        return $this->matchedMissingTranslations;
    }

    public function applySearch(Search $search): void
    {
        if ($search->hasSearchString() === true) {
            $this->matchedTranslations = $this->getTranslationsBySearch($search);
        } else {
            $this->matchedTranslations = $this->getTranslations();
        }
        $this->matchedMissingTranslations = $this->getMissingTranslationsBySearch($search);
    }

    /** @return Translation[] */
    protected function getMissingTranslationsBySearch(Search $search): array
    {
        $filtered = [];
        if ($search->getIncludeSource() === true) {
            if ($search->hasSearchString() === true) {
                foreach ($this->getMissingTranslations() as $translation) {
                    if ($translation->matchSearch($search) === true) {
                        $filtered[] = $translation;
                    }
                }
            } else {
                $filtered = $this->getMissingTranslations();
            }
        }
        return $filtered;
    }

    public function getTranslationFile(): TranslationFile
    {
        return $this->translationFile;
    }

    public function upsertTranslationTarget(Translation $translation): void
    {
        if ($this->hasOwnTranslation($translation) === true) {
            $this->replaceTranslationTarget($translation);
        } elseif ($this->translationFile->hasOwnTranslation($translation) === true) {
            $clonedTranslation = clone $this->translationFile->getOwnTranslation($translation);
            $clonedTranslation->replaceTranslationTargetByOtherTranslation($translation);
            $this->addTranslation($clonedTranslation);
        } else {
            throw new Exception('cannot upsert translation ' . $translation->getTranslationKey(), 1469774422);
        }
    }
}
