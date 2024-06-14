<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Command;

use B13\L10nTranslator\Command\Helper\L10nTranslatorCommand;
use B13\L10nTranslator\Domain\Model\Search;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ListIntegrityCommand extends L10nTranslatorCommand
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->flushCache();
        $search = new Search('', '', '');
        $translationFiles = $this->factory->findBySearch($search);
        $countFiles = 0;
        $countDiffFiles = 0;
        foreach ($translationFiles as $translationFile) {
            $countTranslations = count($translationFile->getTranslations());
            $output->writeln($countTranslations . ' Translations for l10nFile ' . $translationFile->getRelativePath());
            foreach ($translationFile->getL10nTranslationFiles() as $l10nTranslationFile) {
                $countFiles ++;
                $countL10nTranslations = count($l10nTranslationFile->getTranslations());
                $diff = $countTranslations - $countL10nTranslations;
                if ($diff !== 0) {
                    $countDiffFiles ++;
                    $output->writeln('WARNING: ' . $diff . ' labels missing for ' . $l10nTranslationFile->getLanguage());
                }
            }
        }
        if ($countDiffFiles > 0) {
            $output->writeln('WARNING: ' . $countDiffFiles . ' of ' . $countFiles . ' differ');
        }
        return 0;
    }
}
