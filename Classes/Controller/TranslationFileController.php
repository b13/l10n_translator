<?php
declare(strict_types=1);

namespace B13\L10nTranslator\Controller;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Backend\Template\ModuleTemplateFactory;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use B13\L10nTranslator\Utility\StringUtility;
use B13\L10nTranslator\Domain\Factory\TranslationFileFactory;
use B13\L10nTranslator\Exception;
use B13\L10nTranslator\Configuration\L10nConfiguration;
use B13\L10nTranslator\Domain\Model\Search;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TranslationFileController extends ActionController
{
    protected TranslationFileFactory $translationFileFactory;
    protected StringUtility $stringUtility;
    private PageRenderer $pageRenderer;
    private ModuleTemplateFactory $moduleTemplateFactory;

    public function __construct(
        PageRenderer $pageRenderer,
        TranslationFileFactory $translationFileFactory,
        StringUtility $stringUtility,
        ModuleTemplateFactory $moduleTemplateFactory
    ) {
        $this->pageRenderer = $pageRenderer;
        $this->translationFileFactory = $translationFileFactory;
        $this->stringUtility = $stringUtility;
        $this->moduleTemplateFactory = $moduleTemplateFactory;
    }

    protected function initializeListAction(): void
    {
        if ($this->request->hasArgument('search')) {
            $propertyMappingConfiguration = $this->arguments['search']->getPropertyMappingConfiguration();
            $propertyMappingConfiguration->allowProperties('language');
            $propertyMappingConfiguration->allowProperties('l10nFile');
            $propertyMappingConfiguration->allowProperties('searchString');
            $propertyMappingConfiguration->allowProperties('exactMatch');
            $propertyMappingConfiguration->allowProperties('caseInSensitive');
            $propertyMappingConfiguration->allowProperties('includeSource');
            $propertyMappingConfiguration->allowProperties('includeKey');
            // for searching from table row and don't set the checkbox for exact match
            $propertyMappingConfiguration->allowProperties('onlyOneTimeExactSearch');
        }
    }

    public function listAction(Search $search = null): ResponseInterface
    {
        $l10nConfiguration = GeneralUtility::makeInstance(L10nConfiguration::class);
        $this->pageRenderer->loadRequireJsModule('TYPO3/CMS/L10nTranslator/L10nTranslator');
        $translationFiles = [];
        $availableL10nFiles = $l10nConfiguration->getAvailableL10nFiles();
        $availableLanguages = $l10nConfiguration->getAvailableL10nLanguages();
        $languages = [];
        foreach ($availableLanguages as $availableLanguage) {
            $languages[$availableLanguage] = $availableLanguage;
        }
        $l10nFiles = [];
        foreach ($availableL10nFiles as $availableL10nFile) {
            $l10nFiles[$availableL10nFile] = $this->stringUtility->stripPathToLanguageFile($availableL10nFile);
        }
        $this->view->assign('l10nFiles', $l10nFiles);
        $this->view->assign('languages', $languages);
        if ($search !== null) {
            try {
                $translationFiles = $this->translationFileFactory->findBySearch($search);
            } catch (Exception $e) {
                $this->addFlashMessage($e->getMessage() . ' - ' . $e->getCode(), '', FlashMessage::ERROR);
            }
        }
        if ($search !== null) {
            if ($search->checkIfIgnoreExactMatchInView()) {
                $this->addFlashMessage('', 'Search with exact match', FlashMessage::INFO);
            };
        }
        $this->view->assign('search', $search);
        $this->view->assign('translationFiles', $translationFiles);
        $moduleTemplate = $this->moduleTemplateFactory->create($this->request);
        return $this->htmlResponse($moduleTemplate->setContent($this->view->render())->renderContent());
    }
}
