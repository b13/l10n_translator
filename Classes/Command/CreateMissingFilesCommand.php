<?php
declare(strict_types=1);

namespace B13\L10nTranslator\Command;

use B13\L10nTranslator\Domain\Model\Exception;
use B13\L10nTranslator\Command\Helper\L10nTranslatorCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMissingFilesCommand extends L10nTranslatorCommand
{
    public function configure(): void
    {
        $this->setDescription('Creates missing locallang files for one or all languages. It will copy the default labels if you dont set the "emptyFile" option.');
        $this->addArgument(
            'language',
            InputArgument::REQUIRED,
            'The language isocode to create missing files in. Use "all" for all languages that have files configured. Use "system" for all system extensions.'
        )
        ->addOption(
            'emptyFile',
            'e',
            InputOption::VALUE_NONE,
            'Do not copy labels from default language'
        );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $language = $input->getArgument('language');
        $copyLabels = !(bool)$input->getOption('emptyFile');
        $this->flushCache();
        if ($language === 'all') {
            $languages = $this->getAllConfiguredLanguages();
            foreach ($languages as $language) {
                try {
                    $this->translationFileService->createMissingFiles($language, $copyLabels);
                } catch (Exception $e) {
                    $output->writeln($e->getMessage());
                }
            }
        } elseif ($language === 'system') {
            $languages = $this->getAllSystemLanguages();
            foreach ($languages as $language) {
                try {
                    $this->translationFileService->createMissingFiles($language, $copyLabels);
                } catch (Exception $e) {
                    $output->writeln($e->getMessage());
                }
            }
        } else {
            $this->translationFileService->createMissingFiles(
                $language,
                $copyLabels
            );
        }
        $this->flushCache();
        return 0;
    }
}
