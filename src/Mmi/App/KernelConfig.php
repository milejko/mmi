<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

/**
 * Klasa konfiguracji aplikacji
 */
abstract class KernelConfig {
	
	/**
	 * Konfiguracja postawowego cache
	 * @var \Mmi\Cache\CacheConfig
	 */
	public $cache;

	/**
	 * Konfiguracji bazy danych
	 * @var \Mmi\Db\DbConfig
	 */
	public $db;

	/**
	 * Konfiguracja loggera
	 * @var \Mmi\Log\LogConfig
	 */
	public $log;

	/**
	 * Konfiguracja routera
	 * @var \Mmi\Mvc\RouterConfig
	 */
	public $router;

	/**
	 * Konfiguracja sesji
	 * @var \Mmi\Session\SessionConfig
	 */
	public $session;

	/**
	 * Charset
	 * @var string
	 */
	public $charset = 'utf-8';

	/**
	 * Tryb debugowania
	 * @var boolean
	 */
	public $debug = false;
	
	/**
	 * Bezwarunkowa kompilacja
	 * @var boolean
	 */
	public $compile = false;

	/**
	 * Strefa czasowa
	 * @var string
	 */
	public $timeZone = 'Europe/Warsaw';

	/**
	 * Globalna sól aplikacji
	 * @var string
	 */
	public $salt = 'change-this-value';

	/**
	 * Języki obsługiwane przez aplikację
	 * np. pl, en, fr
	 * @var array
	 */
	public $languages = [];

	/**
	 * Pluginy włączone w aplikacji
	 * np. MmiTest\Controller\Plugin
	 * @var array
	 */
	public $plugins = [];

	/**
	 * Domyślny host, jeśli nie ustawiony
	 * @var string
	 */
	public $host = 'localhost';

	/**
	 * Konstruktor konfiguracji
	 */
	public function __construct() {
		//konfiguracja bufora
		$this->cache = new \Mmi\Cache\CacheConfig;
		//konfiguracja bazy danych
		$this->db = new \Mmi\Db\DbConfig;
		//ustawienia loggera
		$this->log = new \Mmi\Log\LogConfig;
		//ustawienia routera
		$this->router = new \Mmi\Mvc\RouterConfig;
		//konfiguracja sesji
		$this->session = new \Mmi\Session\SessionConfig;
	}

}
