<?php

namespace Mmi\Console;

use Mmi\App\App;
use Mmi\Cache\Cache;
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
        try {
            //czyszczenie bufora systemowego
            App::$di->get('PrivateCacheService')->flush();
            //czyszczenie bufora aplikacyjnego
            App::$di->get(Cache::class)->flush();
        } catch (\Exception $e) {
            $output->writeln('Error');
            return 1;
        }
        $output->writeln('Cache flushed');
        return 0;
    }

}