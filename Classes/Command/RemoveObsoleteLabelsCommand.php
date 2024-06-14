<?php

declare(strict_types=1);

namespace B13\L10nTranslator\Command;

use B13\L10nTranslator\Command\Helper\L10nTranslatorCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveObsoleteLabelsCommand extends L10nTranslatorCommand
{
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->flushCache();
        $this->translationFileService->removeAllObsoleteLabels();
        return 0;
    }
}
