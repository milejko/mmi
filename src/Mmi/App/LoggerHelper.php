<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

use Monolog\Logger;
use Monolog\Handler;

class LoggerHelper {

	/**
	 * Monolog instance to hold and use
	 * @var \Monolog\Logger
	 */
	protected static $_loggerInstance;
	
	/**
	 * Nazwa loggera
	 * @var string
	 */
	protected static $_name = 'App';
	
	/**
	 * Poziom logowania
	 * @var integer
	 */
	protected static $_level = Logger::DEBUG;
	
	/**
	 * Ścieżka logowania
	 * @var type 
	 */
	protected static $_path = \BASE_PATH . '/var/log/app.log';
	
	/**
	 * Handler logowania
	 * @var type 
	 */
	protected static $_handler = 'stream';
	
	/**
	 * Token
	 * @var string
	 */
	protected static $_token;

	/**
	 * Ustawia poziom logowania
	 * @param integer $level
	 */
	public static function setLevel($level) {
		self::$_level = Logger::DEBUG;
		switch ($level) {
			case Logger::DEBUG:
			case Logger::INFO:
			case Logger::NOTICE:
			case Logger::WARNING:
			case Logger::ERROR:
			case Logger::CRITICAL:
			case Logger::ALERT:
			case Logger::EMERGENCY:
				self::$_level = $level;
			break;
		}
	}
	
	/**
	 * Zwraca poziom logowania
	 * @return integer
	 */
	public static function getLevel() {
		return self::$_level;
	}
	
	/**
	 * Ustawia nazwę loggera
	 * @param string $name
	 */
	public static function setName($name) {
		self::$_name = $name;
	}
	
	/**
	 * Token
	 * @param type $token
	 */
	public static function setToken($token) {
		self::$_token = $token;
	}
	
	/**
	 * Ustawia ścieżkę logowania
	 * @param string $path
	 */
	public static function setPath($path) {
		self::$_path = $path;
	}
	
	/**
	 * Ustawia nazwę handlera
	 * @param string $handler stream lub gelf
	 */
	public static function setHandler($handler) {
		self::$_handler = $handler;
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
	 * @return \Mmi\App\LoggerHelper
	 */
	private static function _configureLogger() {
		//nowy obiekt loggera
		$logger = new Logger(self::$_name);
		//wybór handlera
		switch (self::$_handler) {
			case 'stream':
				$logger->pushHandler(new Handler\StreamHandler(self::$_path, self::$_level));
				break;
			case 'gelf':
				$logger->pushHandler(new Handler\GelfHandler(new \Gelf\Publisher(self::$_path)));
				break;
			case 'slack':
				$logger->pushHandler(new Handler\SlackHandler(self::$_token, self::$_path, self::$_name, false, '😂', self::$_level));
				break;
			default:
				throw new Exception('Unknown logger handler');
		}
		return $logger;
	}

}
