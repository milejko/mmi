<?php

namespace Mmi\Command;

use Mmi\Cache\Cache;
use Mmi\Cache\SystemCacheInterface;
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
     * @var SystemCacheInterface
     */
    private $systemCache;

    public function __construct(Cache $cache, SystemCacheInterface $systemCache)
    {
        $this->cache        = $cache;
        $this->systemCache  = $systemCache;
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
        $this->systemCache->flush();
        //czyszczenie bufora aplikacyjnego
        $this->cache->flush();
        $output->writeln('Cache flushed');
        return 0;
    }

}