<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Command;

use B13\L10nTranslator\Command\Helper\L10nTranslatorCommand;
use B13\L10nTranslator\Domain\Model\Search;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowIntegrityCommand extends L10nTranslatorCommand
{
    public function configure(): void
    {
        $this->setDescription('Lists missing labels for a given language.')
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
            );
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->flushCache();
        $search = new Search('', $input->getOption('language'), $input->getOption('file'));
        $translationFiles = $this->factory->findBySearch($search);
        foreach ($translationFiles as $translationFile) {
            foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
                foreach ($translationFile->getTranslations() as $translation) {
                    if ($l10nTranslationFile->hasOwnTranslation($translation) === false) {
                        $output->writeln('WARNING: ' . $translation->getTranslationKey() . ' - ' . $translation->getTranslationSource());
                    }
                }
            }
        }
        return 0;
    }
}
