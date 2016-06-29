<?php
namespace Lightwerk\L10nTranslator\Domain\Factory;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2016 Achim Fritz <af@achimfritz.de>, Lightwerk GmbH
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
use Lightwerk\L10nTranslator\Domain\Model\Search;
use Lightwerk\L10nTranslator\Domain\Model\TranslationFile;
use TYPO3\CMS\Core\SingletonInterface;

/**
 * @package TYPO3
 * @subpackage l10n_translator
 */
class TranslationFileFactory implements SingletonInterface
{

    /**
     * @var \Lightwerk\L10nTranslator\Configuration\L10nConfiguration
     * @inject
     */
    protected $l10nConfiguration;

    /**
     * @var \TYPO3\CMS\Core\Localization\LocalizationFactory
     * @inject
     */
    protected $localizationFactory;
    
    /**
     * @param $relativPath
     * @return TranslationFile
     * @throws Exception
     */
    public function findByRelativPath($relativPath) {
        $splFileInfo = new \SplFileInfo(PATH_site . TranslationFile::FOLDER . DIRECTORY_SEPARATOR . $relativPath);
        if ($splFileInfo->isFile() === FALSE) {
            throw new Exception('cannot create splFileInfo with path ' . $relativPath, 1466093531);
        }
        $translationFile = new TranslationFile();
        $translationFile->initFileSystem($splFileInfo, $this->l10nConfiguration->getAvailableL10nLanguages(), $this->localizationFactory);
        return $translationFile;
    }

    /**
     * @param $path
     * @return TranslationFile
     * @throws Exception
     */
    public function findByPath($path)
    {
        try {
            $translationFile = $this->findByRelativPath($path);
        } catch (Exception $e) {
            $splFileInfo = new \SplFileInfo($path);
            if ($splFileInfo->isFile() === FALSE) {
                throw new Exception('cannot create splFileInfo with path ' . $path, 1466093537);
            }
            $translationFile = new TranslationFile();
            $translationFile->initFileSystem($splFileInfo, $this->l10nConfiguration->getAvailableL10nLanguages(), $this->localizationFactory);
        }
        return $translationFile;
    }


    /**
     * @param Search $search
     * @return array<TranslationFile>
     * @throws Exception
     */
    public function findBySearch(Search $search)
    {
        $translationFiles = array();
        $availableL10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        if ($search->getLanguage() !== '') {
            $languages = array($search->getLanguage());
        } else {
            $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        }
        if ($search->getL10nFile() !== '') {
            $availableL10nFiles = array($search->getL10nFile());
        }
        foreach ($availableL10nFiles as $availableL10nFile) {
            $path = PATH_site . TranslationFile::FOLDER . DIRECTORY_SEPARATOR . $availableL10nFile;
            $translationFile = new TranslationFile();
            $translationFile->initFileSystem(new \SplFileInfo($path), $languages, $this->localizationFactory);
            $translationFile->applySearch($search);
            $translationFiles[] = $translationFile;
        }
        return $translationFiles;
    }


}