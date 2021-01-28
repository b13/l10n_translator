<?php
declare(strict_types=1);

namespace B13\L10nTranslator\Command;

use B13\L10nTranslator\Command\Helper\L10nTranslatorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMissingSourceTagsCommand extends L10nTranslatorCommand
{
    public function configure(): void
    {
        $this->setDescription('Creates missing source tags in locallang files for one or all languages.');
        $this->addArgument(
            'language',
            InputArgument::REQUIRED,
            'The language isocode to create missing files in. Use "all" for all languages that have files configured.'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $language = $input->getArgument('language');
        if ($language === 'all') {
            $this->translationFileService->createSourceTagsForAllFilesAndLanguages();
        } else {
            $this->translationFileService->createSourceTagsForAllFiles($language);
        }
        $this->flushCache();
        return 0;
    }
}
