<?php
declare(strict_types=1);

namespace B13\L10nTranslator\Command\Helper;

use B13\L10nTranslator\Configuration\L10nConfiguration;
use B13\L10nTranslator\Domain\Factory\TranslationFileFactory;
use B13\L10nTranslator\Domain\Service\TranslationFileService;
use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class L10nTranslatorCommand extends Command
{
    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @var TranslationFileService
     */
    protected $translationFileService;

    /**
     * @var TranslationFileFactory
     */
    protected $factory;

    public function injectTranslationFileFactory(TranslationFileFactory $factory): void
    {
        $this->factory = $factory;
    }

    public function injectCacheManager(CacheManager $cacheManager): void
    {
        $this->cacheManager = $cacheManager;
    }

    public function injectTranslationFileService(TranslationFileService $translationFileService): void
    {
        $this->translationFileService = $translationFileService;
    }

    protected function flushCache(): void
    {
        $cacheFrontend = $this->cacheManager->getCache('l10n');
        $cacheFrontend->flush();
    }

    protected function getAllSystemLanguages(): array
    {
        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_language');
        $rows = $queryBuilder->select('language_isocode')
            ->from('sys_language')
            ->execute()
            ->fetchAll();
        $rows = $rows ?: [];
        return array_unique(array_column($rows, 'language_isocode'));
    }

    protected function getAllConfiguredLanguages(): array
    {
        return GeneralUtility::makeInstance(L10nConfiguration::class)->getAvailableL10nLanguages();
    }
}
