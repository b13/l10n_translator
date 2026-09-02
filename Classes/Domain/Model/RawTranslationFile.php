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

use TYPO3\CMS\Core\Localization\Parser\XliffParser;

/**
 * Representation of an original translation file within an extension.
 * Without any overlays from the var/labels folder applied.
 *
 * This is used to remove obsolete labels from the L10nTranslationFiles
 * that are no longer present in the TranslationFile itself.
 */
class RawTranslationFile extends TranslationFile
{
    protected function getParsedData(XliffParser $xliffParser): array
    {
        return $xliffParser->getParsedData($this->getCleanPath(), $this->getLanguage());
    }

    public function initFileSystem(\SplFileInfo $splFileInfo, array $languages, XliffParser $xliffParser): void
    {
        parent::initFileSystem($splFileInfo, $languages, $xliffParser);
        foreach ($this->l10nTranslationFiles as $l10nTranslationFile) {
            $l10nTranslationFile->removeObsoleteTranslations();
        }
    }
}
