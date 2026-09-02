<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Command\Helper;

use B13\L10nTranslator\Configuration\L10nConfiguration;
use B13\L10nTranslator\Domain\Factory\TranslationFileFactory;
use B13\L10nTranslator\Domain\Service\TranslationFileService;
use Symfony\Component\Console\Command\Command;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class L10nTranslatorCommand extends Command
{
    public function __construct(
        protected readonly TranslationFileFactory $factory,
        protected readonly CacheManager $cacheManager,
        protected readonly TranslationFileService $translationFileService,
        ?string $name = null,
        ?callable $code = null
    ) {
        parent::__construct($name, $code);
    }

    protected function flushCache(): void
    {
        $cacheFrontend = $this->cacheManager->getCache('l10n');
        $cacheFrontend->flush();
    }

    protected function getAllSystemLanguages(): array
    {
        $languages = [];
        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        foreach ($siteFinder->getAllSites() as $site) {
            foreach ($site->getLanguages() as $siteLanguage) {
                $languages[] = $siteLanguage->getTypo3Language();
            }
        }
        return array_unique($languages);
    }

    protected function getAllConfiguredLanguages(): array
    {
        return GeneralUtility::makeInstance(L10nConfiguration::class)->getAvailableL10nLanguages();
    }
}
