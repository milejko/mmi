<?php

namespace Mmi\Command;

use DI\Container;
use Mmi\Cache\SystemCacheInterface;
use Mmi\Mvc\Structure;
use Symfony\Component\Console\Application as BaseApplication;

class Application extends BaseApplication
{

    /**
     * @var Container
     */
    private $container;

    /**
     * Application constructor.
     */
    public function __construct(Container $container)
    {
        parent::__construct('MMi Console', '4.0');
        $this->container = $container;
    }

    /**
     * @return array
     */
    protected function getDefaultCommands()
    {
        return array_merge(parent::getDefaultCommands(), $this->getApplicationCommands());
    }

    /**
     * Pobieranie komend z aplikacji
     */
    protected function getApplicationCommands()
    {
        $commands = [];
        foreach ($this->getApplicationCommandNames() as $commandName) {
            $commands[] = $this->container->get($commandName);
        }
        return $commands;
    }

    private function getApplicationCommandNames(): array
    {
        if (null !== $commands = $this->container->get(SystemCacheInterface::class)->load($cacheKey = 'mmi-commands')) {
            return $commands;
        }
        //iterating classes
        foreach (Structure::getStructure('classes') as $entryName) {
            if (0 !== \preg_match('/^[a-zA-Z0-9]+\\\Command\\\[a-zA-Z0-9]+Command$/', $entryName)) {
                $commands[] = $entryName;
            }
        }
        $this->container->get(SystemCacheInterface::class)->save($commands, $cacheKey);
        return $commands;
    }

}