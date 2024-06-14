<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Controller\Ajax;

/*
 * This file is part of TYPO3 CMS-based extension l10n_translator by b13.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 */

use B13\L10nTranslator\Configuration\L10nConfiguration;
use B13\L10nTranslator\Domain\Factory\TranslationFileFactory;
use B13\L10nTranslator\Domain\Model\Translation;
use B13\L10nTranslator\Domain\Service\TranslationFileService;
use B13\L10nTranslator\Domain\Service\TranslationFileWriterService;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Messaging\AbstractMessage;

class TranslationController
{
    protected TranslationFileFactory $translationFileFactory;
    protected L10nConfiguration $l10nConfiguration;
    protected TranslationFileWriterService $translationFileWriterService;
    protected CacheManager $cacheManager;
    protected TranslationFileService $translationFileService;

    public function __construct(
        L10nConfiguration $l10nConfiguration,
        TranslationFileFactory $translationFileFactory,
        TranslationFileWriterService $translationFileWriterService,
        TranslationFileService $translationFileService,
        CacheManager $cacheManager
    ) {
        $this->l10nConfiguration = $l10nConfiguration;
        $this->translationFileFactory = $translationFileFactory;
        $this->translationFileWriterService = $translationFileWriterService;
        $this->translationFileService = $translationFileService;
        $this->cacheManager = $cacheManager;
    }

    public function update(ServerRequestInterface $request): JsonResponse
    {
        $this->assureModuleAccess();
        $postParams = $request->getParsedBody();
        $this->validateRequest($postParams);
        $translationFile = $this->translationFileFactory->findByPath($postParams['path']);
        $l10nTranslationFile = $translationFile->getL10nTranslationFile($postParams['language']);
        $translation = new Translation($postParams['path'], $postParams['key'], $postParams['target']);
        $l10nTranslationFile->upsertTranslationTarget($translation);
        $this->translationFileWriterService->writeTranslation($l10nTranslationFile);
        if ($postParams['language'] === 'default') {
            $this->translationFileService->updateSourceInFiles($postParams);
        }
        $this->flushCache();
        $content = [
            'flashMessage' => [
                'title' => 'OK',
                'message' => 'label updated',
                'severity' => AbstractMessage::OK,
            ],
        ];
        try {
        } catch (\Exception $e) {
            $content = [
                'flashMessage' => [
                    'title' => 'ERROR',
                    'message' => $e->getMessage() . ' - ' . $e->getCode(),
                    'severity' => AbstractMessage::ERROR,
                ],
            ];
        }

        return new JsonResponse($content);
    }

    protected function assureModuleAccess(): void
    {
        $beUser = $this->getBeUser();
        if ($beUser->check('modules', 'web_L10nTranslatorTranslator') === false) {
            throw new Exception('Access Denied', 1469781234);
        }
    }

    protected function getBeUser(): BackendUserAuthentication
    {
        return $GLOBALS['BE_USER'];
    }

    protected function flushCache(): void
    {
        $cacheFrontend = $this->cacheManager->getCache('l10n');
        $cacheFrontend->flush();
    }

    /**
     * @param mixed $postParams
     */
    protected function validateRequest($postParams): void
    {
        if (!is_array($postParams)) {
            throw new Exception('Invalid request.', 1467175555);
        }

        if (isset($postParams['language']) === false || isset($postParams['target']) === false || isset($postParams['key']) === false || isset($postParams['path']) === false) {
            throw new Exception('Invalid request.', 1467175555);
        }
        $languages = $this->l10nConfiguration->getAvailableL10nLanguages();
        $l10nFiles = $this->l10nConfiguration->getAvailableL10nFiles();
        if (in_array($postParams['language'], $languages) === false) {
            throw new Exception('Language not configured: ' . $postParams['language'], 1467175550);
        }
        if (in_array($postParams['path'], $l10nFiles) === false) {
            throw new Exception('Path not configured: ' . $postParams['path'], 1467175551);
        }
        if (empty($postParams['key']) === true) {
            throw new Exception('Key must not be empty.', 1467175554);
        }
        if ($postParams['target'] !== strip_tags($postParams['target']) && !$this->l10nConfiguration->isHtmlAllowed()) {
            throw new Exception('HTML not allowed.', 1467175552);
        }
    }
}
