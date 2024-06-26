<?php

declare(strict_types=1);

namespace B13\L10nTranslator\ViewHelpers;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

class IfShouldBeTextAreaViewHelper extends AbstractConditionViewHelper
{
    const STRLEN_FOR_TEXTAREA = 50;

    public function initializeArguments(): void
    {
        parent::initializeArguments();
        $this->registerArgument('source', 'string', 'The source of the current label', true);
    }

    /**
     * Returns true if the $arguments['input'] string either
     *   * contains a line break
     *   * exceeds 50 characters
     *
     * @param array $arguments
     */
    protected static function evaluateCondition($arguments = null): bool
    {
        return isset($arguments['source']) && (strpos($arguments['source'], PHP_EOL) !== false || strlen($arguments['source']) > self::STRLEN_FOR_TEXTAREA);
    }
}
