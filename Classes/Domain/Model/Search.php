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
    public function __construct(
        readonly protected string $searchString = '',
        readonly protected string $language = '',
        readonly protected string $l10nFile = '',
        readonly protected bool $caseSensitive = false,
        protected bool $exactMatch = false,
        readonly protected bool $includeSource = true,
        readonly protected bool $includeKey = true,
        /** For unmark the flag in the exactSearch, if the search come from the link of the defaultSource */
        readonly protected bool $onlyOneTimeExactSearch = false
    ) {
    }

    public function getSearchString(): string
    {
        return $this->searchString;
    }

    public function getLanguage(): string
    {
        return $this->language;
    }

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
