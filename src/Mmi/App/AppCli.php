<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Mmi\Command\Application;

/**
 * CLI application class
 */
class AppCli extends App
{

    /**
     * Application run
     */
    public function run(): void
    {
        //can be executed only via cli
        ('cli' == PHP_SAPI) || die('CLI commands are only available to run via the console');
        //unlimited execution time and large memory limit - 2GB
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', '2048M');

        //@TODO: remove after target refactoring
        self::$di = $this->container;
        $interceptor = $this->container->has(AppEventInterceptorAbstract::class) ? $this->container->get(AppEventInterceptorAbstract::class) : null;
        $application = new Application($this->container);
        $application->run();
    }

}
