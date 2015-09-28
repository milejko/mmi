<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Log;

use Monolog\Logger;
use Monolog\Handler;

/**
 * Klasa nakładki na Monolog
 */
class LoggerHelper {

	/**
	 * Monolog instance to hold and use
	 * @var \Monolog\Logger
	 */
	protected static $_loggerInstance;
	
	/**
	 * Konfiguracja loggera
	 * @var Config
	 */
	protected static $_config;
	
	/**
	 * Ustawia konfigurację loggera
	 * @param \Mmi\Logger\Config $config
	 */
	public static function setConfig(Config $config) {
		self::$_config = $config;
	}
	
	/**
	 * Zwraca poziom logowania
	 * @return integer
	 */
	public static function getLevel() {
		if (!self::$_config) {
			throw new Exception('Configuration not loaded');
		}
		return self::$_config->level;
	}
	
	/**
	 * Zwraca instancję helpera logowania
	 * @return \Monolog\Logger
	 */
	public static function getLogger() {
		if (self::$_loggerInstance) {
			return self::$_loggerInstance;
		}
		//konfiguruje loggera
		return self::$_loggerInstance = self::_configureLogger();
	}
	
	/**
	 * Konfiguruje loggera
	 * @return \Mmi\Logger\LoggerHelper
	 */
	private static function _configureLogger() {
		if (!self::$_config) {
			throw new Exception('Configuration not loaded');
		}
		//nowy obiekt loggera
		$logger = new Logger(self::$_config->name);
		//wybór handlera
		switch (self::$_config->handler) {
			case 'stream':
				$logger->pushHandler(new Handler\StreamHandler(self::$_config->path, self::$_config->level));
				break;
			case 'gelf':
				$logger->pushHandler(new Handler\GelfHandler(new \Gelf\Publisher(self::$_config->path)));
				break;
			case 'slack':
				$logger->pushHandler(new Handler\SlackHandler(self::$_config->token, self::$_config->path, self::$_config->name, false, '😂', self::$_config->level));
				break;
			case 'console':
				$logger->pushHandler(new Handler\PHPConsoleHandler([], null, self::$_config->level));
				break;
			default:
				throw new Exception('Unknown handler');
		}
		return $logger;
	}

}
