<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Throwable;

/**
 * Application exception logger interface
 */
interface AppExceptionLoggerInterface
{

    /**
     * Exception logger
     */
    public function logException(Throwable $exception): void;

 }