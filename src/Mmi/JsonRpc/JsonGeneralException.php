<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\JsonRpc;

/**
 * Klasa wyjątków ogólnych serwera JSON-RPC
 */
class JsonGeneralException extends JsonException
{

    protected $code = 250;

}
