<?php

namespace Mmi\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FlushCacheCommand extends CommandAbstract
{

    public function configure()
    {
        $this->setName('cache:flush');
        $this->setDescription('Flush cache');
        parent::configure();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        //czyszczenie bufora systemowego
        \Mmi\App\FrontController::getInstance()->getLocalCache()->flush();
        //czyszczenie bufora aplikacyjnego
        \App\Registry::$cache->flush();

        $output->writeln('Cache flushed');
    }

}