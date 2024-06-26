<?php

namespace B13\L10nTranslator\Tests\Unit\Domain\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use B13\L10nTranslator\Domain\Model\TranslationFile;
use B13\L10nTranslator\Domain\Service\TranslationFileWriterService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TranslationFileWriterServiceTest extends UnitTestCase
{
    /**
     * @test
     * @expectedException \B13\L10nTranslator\Domain\Service\Exception
     */
    public function assureValidXmlThrowsExceptionForInvalidXml()
    {
        $translationFile = $this->getAccessibleMock(TranslationFile::class);
        $fileWriter = $this->getAccessibleMock(TranslationFileWriterService::class, ['foo']);
        $fileWriter->_call('assureValidXml', 'foo', $translationFile);
    }
}
