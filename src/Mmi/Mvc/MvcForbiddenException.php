<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\Log\LogConfigInstance;

/**
 * Klasa wyjątku niedozwolonego miejsca mvc
 */
class MvcForbiddenException extends MvcException
{

    /**
     * Poziom logowania
     * @var integer
     */
    protected $code = LogConfigInstance::INFO;

    /**
     * Konstruktor
     * @param string $message
     * @param integer $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = LogConfigInstance::INFO, \Exception $previous = null)
    {
        //ignorowanie transakcji
        extension_loaded('newrelic') ? newrelic_ignore_transaction() : null;
        parent::__construct($message, $code, $previous);
    }

}
