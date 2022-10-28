<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

/**
 * CLI error & exception handler
 */
class AppCliErrorHandler
{
    /**
     * @var AppExceptionLogger
     */
    private $logger;

    /**
     * Konstruktor podpinający eventy
     */
    public function __construct(AppExceptionLogger $logger)
    {
        //assigning injections
        $this->logger  = $logger;
    }

    /**
     * Errors, warnings, notices, etc. as exception
     * @throws KernelException
     */
    public function errorHandler(string $errno, string $errstr, string $errfile, string $errline): void
    {
        throw new KernelException($errno . ': ' . $errstr . '[' . $errfile . ' (' . $errline . ')]');
    }

    /**
     * Obsługuje wyjątki
     */
    public function exceptionHandler($exception): void
    {
        //logowanie wyjątku
        $this->logger->logException($exception);
        echo 'Something went wrong';
    }
}
