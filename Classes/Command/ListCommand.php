<?php
declare(strict_types=1);

namespace B13\L10nTranslator\Command;

use B13\L10nTranslator\Command\Helper\L10nTranslatorCommand;
use B13\L10nTranslator\Domain\Model\Search;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ListCommand extends L10nTranslatorCommand
{
    public function configure(): void
    {
        $this
            ->setDescription('Lists all labels that match the query.')
            ->addOption(
                'searchString',
                null,
                InputOption::VALUE_REQUIRED,
                'Search string for finding labels',
                ''
            )
            ->addOption(
                'language',
                null,
                InputOption::VALUE_REQUIRED,
                'Limit to a language ID',
                ''
            )
            ->addOption(
                'file',
                null,
                InputOption::VALUE_REQUIRED,
                'Limit to a locallang file',
                ''
            )
            ->addOption(
                'caseSensitive',
                'c',
                InputOption::VALUE_NONE,
                'Make search case sensitive'
            )
            ->addOption(
                'exactMatch',
                'x',
                InputOption::VALUE_NONE,
                'Only find exact matches'
            )
            ->addOption(
                'source',
                's',
                InputOption::VALUE_NONE,
                'Include source labels when searching'
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $search = new Search(
            $input->getOption('searchString'),
            $input->getOption('language'),
            $input->getOption('file'),
            $input->getOption('caseSensitive'),
            $input->getOption('exactMatch'),
            $input->getOption('source')
        );
        $translationFiles = $this->factory->findBySearch($search);
        foreach ($translationFiles as $translationFile) {
            foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
                $translations = $l10nTranslationFile->getMatchedTranslations();
                foreach ($translations as $translation) {
                    $output->writeln($l10nTranslationFile->getLanguage() . ' ' . $translation->getTranslationKey() . ': ' . $translation->getTranslationTarget());
                }
                $translations = $l10nTranslationFile->getMatchedMissingTranslations();
                foreach ($translations as $translation) {
                    $output->writeln($l10nTranslationFile->getLanguage() . ' ' . $translation->getTranslationKey() . ': ' . $translation->getTranslationTarget());
                }
            }
        }
        return 0;
    }
}
