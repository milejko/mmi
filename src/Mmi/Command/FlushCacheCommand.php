<?php

namespace Mmi\Command;

use Mmi\App\App;
use Mmi\Cache\Cache;
use Mmi\Cache\PrivateCache;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Flush cache
 */
class FlushCacheCommand extends CommandAbstract
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var PrivateCache
     */
    private $privateCache;

    public function __construct(Cache $cache, PrivateCache $privateCache)
    {
        $this->cache        = $cache;
        $this->privateCache = $privateCache;
        parent::__construct();
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        //czyszczenie bufora systemowego
        $this->privateCache->flush();
        //czyszczenie bufora aplikacyjnego
        $this->cache->flush();
        $output->writeln('Cache flushed');
        return 0;
    }

}