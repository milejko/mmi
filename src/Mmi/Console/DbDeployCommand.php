<?php

namespace Mmi\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class DbDeployCommand extends CommandAbstract
{

    public function configure()
    {
        $this->setName('db:deploy');
        $this->setDescription('Deploy database incremental');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        (new \Mmi\Db\Deployer)->deploy();
    }

}