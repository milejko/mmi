<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Throwable;

/**
 * Application error handler interface
 */
interface AppErrorHandlerInterface
{

    /**
     * Errors, warnings, notices, etc. as exception
     */
    public function errorHandler(string $errno, string $errstr, string $errfile, string $errline): void;

    /**
     * Obsługuje wyjątki
     */
    public function exceptionHandler(Throwable $exception): void;

}
