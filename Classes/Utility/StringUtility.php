<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Utility;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3\CMS\Core\SingletonInterface;

class StringUtility implements SingletonInterface
{
    /**
     * Strips the path to a language file off the string and appends the
     * filename into parentheses.
     *
     * E.g.:
     * news/Resources/Private/Language/locallang.xlf => news (locallang.xlf)
     */
    public function stripPathToLanguageFile(string $fullPath): string
    {
        $pathsToStrip = [
            '/Resources/Private/Language/',
            '/pi1/',
        ];

        foreach ($pathsToStrip as $pathToStrip) {
            if (strpos($fullPath, $pathToStrip) !== false) {
                $stripedString = str_replace($pathToStrip, ' (', $fullPath) . ')';
                return $stripedString;
            }
        }

        return $fullPath;
    }
}
