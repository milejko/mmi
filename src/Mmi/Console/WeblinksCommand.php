<?php

namespace Mmi\Console;

use Mmi\App\ComposerInstaller;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class WeblinksCommand extends CommandAbstract
{

    public function configure()
    {
        $this->setName('resource:create-symlinks');
        $this->setDescription('Create symlinks');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        ComposerInstaller::linkModuleWebResources();
        $output->writeln('Symlinks created');
    }

}