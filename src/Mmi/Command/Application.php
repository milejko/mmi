<?php

namespace Mmi\Command;

use DI\Container;
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
        foreach ($this->container->getKnownEntryNames() as $entryName) {
            if (0 !== \preg_match('/\\\Command\\\[a-zA-Z0-9]+Command$/', $entryName)) {
                $commands[] = $this->container->get($entryName);
            }
        }
        return $commands;
    }

}