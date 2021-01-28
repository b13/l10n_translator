<?php
namespace B13\L10nTranslator\Domain\Model;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */
use TYPO3\CMS\Core\Localization\LocalizationFactory;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
abstract class AbstractTranslationFile
{
    /**
     * @var \splFileInfo
     */
    protected $splFileInfo = null;

    /**
     * @var string
     */
    protected $language = '';

    /**
     * @var string
     */
    protected $extension = '';

    /**
     * @var string
     */
    protected $relativePath = '';

    /**
     * @var Translation[]
     */
    protected $translations = [];

    /**
     * @var Translation[]
     */
    protected $matchedTranslations = [];

    public function getCleanPath(): string
    {
        return str_replace('//', '/', $this->getSplFileInfo()->getPathname());
    }

    public function translationsToArray(): array
    {
        $arr = [];
        foreach ($this->getTranslations() as $translation) {
            $arr[$translation->getTranslationKey()] = $translation->getTranslationTarget();
        }
        return $arr;
    }

    /**
     * @param LocalizationFactory $localizationFactory
     */
    protected function initTranslations(LocalizationFactory $localizationFactory): void
    {
        $parsedData = $this->getParsedData($localizationFactory);
        foreach ($parsedData[$this->getLanguage()] as $key => $labels) {
            if (isset($labels[0]['source']) === true && isset($labels[0]['target']) === true) {
                $translation = new Translation($this->getCleanPath(), $key, $labels[0]['target'], $labels[0]['source']);
                $this->translations[] = $translation;
                $this->matchedTranslations[] = $translation;
            }
        }
    }

    /**
     * @param LocalizationFactory $localizationFactory
     */
    abstract protected function getParsedData(LocalizationFactory $localizationFactory): array;

    /**
     * @param Search $search
     * @return Translation[]
     */
    public function getTranslationsBySearch(Search $search): array
    {
        $filtered = [];
        foreach ($this->getTranslations() as $translation) {
            if ($translation->matchSearch($search) === true) {
                $filtered[] = $translation;
            }
        }
        return $filtered;
    }

    /**
     * @param Search $search
     */
    public function hasTranslationOfSearch(Search $search): bool
    {
        foreach ($this->getTranslations() as $translation) {
            if ($translation->matchSearch($search) === true) {
                return true;
            }
        }
        return false;
    }

    /**
     * @param Translation $translation
     */
    public function replaceTranslationTarget(Translation $translation): void
    {
        $replaced = [];
        $currentTranslations = $this->getTranslations();
        foreach ($currentTranslations as $currentTranslation) {
            if ($currentTranslation->getTranslationKey() === $translation->getTranslationKey()) {
                $currentTranslation->replaceTranslationTargetByOtherTranslation($translation);
            }
            $replaced[] = $currentTranslation;
        }
        $this->translations = $replaced;
    }

    /**
     * @param Translation $translation
     */
    public function replaceTranslationSource(Translation $translation): void
    {
        $replaced = [];
        $currentTranslations = $this->getTranslations();
        foreach ($currentTranslations as $currentTranslation) {
            if ($currentTranslation->getTranslationKey() === $translation->getTranslationKey()) {
                $currentTranslation->replaceTranslationSourceByOtherTranslation($translation);
            }
            $replaced[] = $currentTranslation;
        }
        $this->translations = $replaced;
    }

    /**
     * @param Translation $translation
     */
    public function getOwnTranslation(Translation $translation): ?\B13\L10nTranslator\Domain\Model\Translation
    {
        foreach ($this->getTranslations() as $ownTranslation) {
            if ($translation->getTranslationKey() === $ownTranslation->getTranslationKey()) {
                return $ownTranslation;
            }
        }
        return null;
    }

    /**
     * @param Translation $translation
     */
    public function hasOwnTranslation(Translation $translation): bool
    {
        return $this->getOwnTranslation($translation) !== null;
    }

    /**
     * @param Translation $translation
     */
    public function addTranslation(Translation $translation): void
    {
        $this->translations[] = $translation;
    }

    public function getSplFileInfo(): \splFileInfo
    {
        return $this->splFileInfo;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

    public function getExtension(): string
    {
        return $this->extension;
    }

    /**
     * @return Translation[]
     */
    public function getTranslations(): array
    {
        return $this->translations;
    }

    public function getRelativePath(): string
    {
        return $this->relativePath;
    }

    /**
     * @return Translation[]
     */
    public function getMatchedTranslations(): array
    {
        return $this->matchedTranslations;
    }
}
