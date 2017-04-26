<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc;

use Monolog\Logger;

/**
 * Klasa wyjątku niedozwolonego miejsca mvc
 */
class MvcForbiddenException extends MvcException
{

    /**
     * Poziom logowania
     * @var integer
     */
    protected $code = Logger::INFO;

    /**
     * Konstruktor
     * @param string $message
     * @param integer $code
     * @param \Exception $previous
     */
    public function __construct($message = "", $code = 0, \Exception $previous = null)
    {
        //ignorowanie transakcji
        extension_loaded('newrelic') ? newrelic_ignore_transaction() : null;
        parent::__construct($message, $code, $previous);
    }

}
