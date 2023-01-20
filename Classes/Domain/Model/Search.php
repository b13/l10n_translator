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

class Search
{
    /**
     * @var string
     */
    protected $searchString = '';

    /**
     * @var string
     */
    protected $language = '';

    /**
     * @var string
     */
    protected $l10nFile = '';

    /**
     * @var bool
     */
    protected $caseSensitive = false;

    /**
     * @var bool
     */
    protected $exactMatch = false;

    /**
     * @var bool
     */
    protected $includeSource = true;

    /**
     * @var bool
     */
    protected $includeKey = true;

    /**
     * For unmark the flag in the exactSearch, if the search come from the link of the defaultSource
     * @var bool
     */
    protected $onlyOneTimeExactSearch = false;

    /**
     * @param string $searchString
     * @param string $language
     * @param string $l10nFile
     * @param bool $caseSensitive
     * @param bool $exactMatch
     * @param bool $includeSource
     * @param bool $includeKey
     * @param bool $onlyOneTimeExactSearch
     */
    public function __construct(string $searchString = '', string $language = '', string $l10nFile = '', bool $caseSensitive = false, bool $exactMatch = false, bool $includeSource = true, bool $includeKey = true, bool $onlyOneTimeExactSearch = false)
    {
        $this->searchString = $searchString;
        $this->language = $language;
        $this->l10nFile = $l10nFile;
        $this->caseSensitive = $caseSensitive;
        $this->exactMatch = $exactMatch;
        $this->includeSource = $includeSource;
        $this->includeKey = $includeKey;
        $this->onlyOneTimeExactSearch = $onlyOneTimeExactSearch;
    }

    /**
     * @return string $searchString
     */
    public function getSearchString(): string
    {
        return $this->searchString;
    }

    /**
     * @return string $language
     */
    public function getLanguage(): string
    {
        return $this->language;
    }

    /**
     * @return string $l10nFile
     */
    public function getL10nFile(): string
    {
        return $this->l10nFile;
    }

    public function getCaseSensitive(): bool
    {
        return $this->caseSensitive;
    }

    public function getExactMatch(): bool
    {
        return $this->exactMatch;
    }

    public function getIncludeSource(): bool
    {
        return $this->includeSource;
    }

    public function hasLanguage(): bool
    {
        return $this->language !== '';
    }

    public function hasL10nFile(): bool
    {
        return $this->l10nFile !== '';
    }

    public function hasSearchString(): bool
    {
        return $this->searchString !== '';
    }

    public function getIncludeKey(): bool
    {
        return $this->includeKey;
    }

    /**
     * for searching from table row and don't set the checkbox for exact match
     */
    public function checkIfIgnoreExactMatchInView(): bool
    {
        if ($this->onlyOneTimeExactSearch) {
            $this->exactMatch = false;
            return true;
        }
        return false;
    }
}
