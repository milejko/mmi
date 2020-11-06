<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Psr\Log\LoggerInterface;

/**
 * Application exception logger
 */
class AppExceptionLogger
{

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var AppExeptionFormatter
     */
    private $formatter;

    /**
     * Constructor
     */
    public function __construct(
        LoggerInterface $logger, 
        AppExceptionFormatter $formatter
    )
    {
        //service injections
        $this->logger    = $logger;
        $this->formatter = $formatter;
    }

    /**
     * Exception logger
     */
    public function logException($exception): void
    {
        //logowanie wyjątku aplikacyjnego
        if ($exception instanceof KernelException) {
            $this->logger->log($exception->getCode(), $this->formatter->formatException($exception));
            return;
        }
        //logowanie pozostałych wyjątków
        $this->logger->alert($this->formatter->formatException($exception));
    }

 }