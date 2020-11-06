<?php

namespace Mmi\Command;

use Mmi\App\ComposerInstaller;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeblinksCommand extends CommandAbstract
{

    public function configure()
    {
        $this->setDescription('Create symlinks from modules to /web');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        ComposerInstaller::linkModuleWebResources();
        $output->writeln('Symlinks created');
        return 0;
    }

}