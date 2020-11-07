<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Mmi\Cache\PrivateCache;
use Mmi\Mvc\Structure;
use Symfony\Component\Console\Application;

/**
 * CLI application class
 */
class AppCli extends App
{

    private $symfonyConsoleApplication;

    public function __construct()
    {
        //can be executed only via cli
        ('cli' == PHP_SAPI) || die('CLI commands are only available to run via the console');
        $this->symfonyConsoleApplication = new Application('mmi', '4.0');
        parent::__construct();
    }

    /**
     * Application run
     */
    public function run(): void
    {
        //unlimited execution time and large memory limit - 2GB
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        //run interceptor init
        $this->container->has(AppEventInterceptorInterface::class) && $this->container->get(AppEventInterceptorInterface::class)->init();
        //add commands and run
        $this->symfonyConsoleApplication->addCommands($this->getApplicationCommands());
        $this->symfonyConsoleApplication->run();
    }

    /**
     * Gets command instances
     */
    protected function getApplicationCommands()
    {
        $commands = [];
        foreach ($this->getApplicationCommandNames() as $commandName) {
            $commands[] = $this->container->get($commandName);
        }
        return $commands;
    }

    /**
     * Gets command list
     */
    private function getApplicationCommandNames(): array
    {
        if (null !== $commands = $this->container->get(PrivateCache::class)->load($cacheKey = 'mmi-commands')) {
            return $commands;
        }
        //iterating classes
        foreach (Structure::getStructure('classes') as $entryName) {
            if (0 !== \preg_match('/^[a-zA-Z0-9]+\\\Command\\\[a-zA-Z0-9]+Command$/', $entryName)) {
                $commands[] = $entryName;
            }
        }
        $this->container->get(PrivateCache::class)->save($commands, $cacheKey);
        return $commands;
    }

    /**
     * Sets error and exception handler
     */
    protected function setErrorHandler(): self
    {
        //error handler
        set_error_handler([$this->container->get(AppErrorHandler::class), 'errorHandler']);
        //exception handler is not needed (symfony handles it)
        return $this;
    }

}
