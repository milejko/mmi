<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Logger;
use Monolog\Logger;

/**
 * Klasa konfiguracji loggera
 */
class Config {

	/**
	 * Nazwa loggera
	 * @var string
	 */
	public $name = 'App';
	
	/**
	 * Poziom logowania
	 * @var integer
	 */
	public $level = Logger::DEBUG;
	
	/**
	 * Ścieżka logowania
	 * @var string
	 */
	public $path = \BASE_PATH . '/var/log/app.log';
	
	/**
	 * Handler logowania
	 * @var type 
	 */
	public $handler = 'stream';
	
	/**
	 * Token usługi
	 * wymagany tylko w handlerze
	 * @var string
	 */
	public $token;
	
}
