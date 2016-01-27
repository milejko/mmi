<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Log;

use Monolog\Logger;

/**
 * Klasa konfiguracji elementu loggera
 * 
 * @method string getName() pobiera nazwę aplikacji logującej
 * @method LogConfigInstance setName($name) ustawia nazwę aplikacji logującej
 * @method string getPath() pobiera ścieżkę (lub kanał czy IP)
 * @method LogConfigInstance setPath($path) ustawia ścieżkę
 * @method string getHandler() pobiera handler
 * @method string getLevel() pobiera poziom logowania
 * @method string getToken() pobiera token
 * @method LogConfigInstance setToken($path) ustawia token
 */
class LogConfigInstance extends \Mmi\OptionObject {

	/**
	 * Domyślne ustawienia
	 */
	public function __construct() {
		$this->setLevelDebug()
			->setPath(BASE_PATH . '/var/log/app.log')
			->setHandler('stream');
	}

	/**
	 * Poziom na debug
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelDebug() {
		return $this->setOption('level', Logger::DEBUG);
	}

	/**
	 * Poziom na Info
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelInfo() {
		return $this->setOption('level', Logger::INFO);
	}

	/**
	 * Poziom na Notice
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelNotice() {
		return $this->setOption('level', Logger::NOTICE);
	}

	/**
	 * Poziom na Warning
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelWarning() {
		return $this->setOption('level', Logger::WARNING);
	}

	/**
	 * Poziom na Error
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelError() {
		return $this->setOption('level', Logger::ERROR);
	}

	/**
	 * Poziom na Alert
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelAlert() {
		return $this->setOption('level', Logger::ALERT);
	}

	/**
	 * Poziom na Critical
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelCritical() {
		return $this->setOption('level', Logger::CRITICAL);
	}

	/**
	 * Poziom na Emergency
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setLevelEmergency() {
		return $this->setOption('level', Logger::EMERGENCY);
	}

	/**
	 * Ustawia handler na stream
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setHandlerStream() {
		return $this->setOption('handler', 'stream');
	}

	/**
	 * Ustawia handler na slack
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setHandlerSlack() {
		return $this->setOption('handler', 'slack');
	}

	/**
	 * Ustawia handler na stream
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setHandlerConsole() {
		return $this->setOption('handler', 'console');
	}

	/**
	 * Ustawia handler na gelf
	 * @return \Mmi\Log\LogConfigInstance
	 */
	public function setHandlerGelf() {
		return $this->setOption('handler', 'gelf');
	}

}
