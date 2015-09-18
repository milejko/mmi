<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Json\Rpc\General;

/**
 * Klasa wyjątków ogólnych serwera JSON-RPC
 */
class Exception extends \Exception {

	protected $code = -500;

}
