<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Exception;

/**
 * Exception formatter interface
 */
interface AppExceptionFormatterInterface
{

    /**
     * Formatuje obiekt wyjątku do pojedynczej wiadomości
     */
    public function formatException(Exception $exception): string;

    /**
     * Format trace
     */
    public function formatTrace(Exception $exception): string;

}
