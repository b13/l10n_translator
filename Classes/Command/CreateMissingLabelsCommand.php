<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Command;

use B13\L10nTranslator\Command\Helper\L10nTranslatorCommand;
use B13\L10nTranslator\Domain\Model\Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class CreateMissingLabelsCommand extends L10nTranslatorCommand
{
    public function configure(): void
    {
        $this
            ->setDescription('Creates missing labels in one or all files of one or all languages.')
            ->addOption(
                'sourceLanguage',
                null,
                InputOption::VALUE_REQUIRED,
                'Language to use as a source. Defaults to \'default\'.',
                ''
            )
            ->addArgument(
                'language',
                InputArgument::REQUIRED,
                'The target language. Use "all" for all languages that have files configured. Use "system" for all system extensions.'
            )
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'Limit to one locallang file. Defaults to all files.',
                ''
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $language = $input->getArgument('language');
        $languages = [$language];
        if ($language === 'all') {
            $languages = $this->getAllConfiguredLanguages();
        }
        if ($language === 'system') {
            $languages = $this->getAllSystemLanguages();
        }
        $sourceLanguage = $input->getOption('sourceLanguage') ?: 'default';
        $l10nFile = $input->getOption('file') ?: 'all';

        foreach ($languages as $language) {
            try {
                if ($l10nFile === 'all') {
                    $this->translationFileService->createAllMissingLabels($language, $sourceLanguage);
                } else {
                    $this->translationFileService->createMissingLabels($l10nFile, $language, $sourceLanguage);
                }
            } catch (Exception $e) {
                $output->writeln($e->getMessage());
            }
        }
        return 0;
    }
}
