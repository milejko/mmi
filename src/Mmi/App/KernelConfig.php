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
	 * @var \Mmi\Cache\Config
	 */
	public $cache;

	/**
	 * Konfiguracji bazy danych
	 * @var \Mmi\Db\Config
	 */
	public $db;

	/**
	 * Konfiguracja routera
	 * @var \Mmi\Mvc\Router\Config
	 */
	public $router;

	/**
	 * Konfiguracja sesji
	 * @var \Mmi\Session\Config
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
		$this->cache = new \Mmi\Cache\Config();
		//konfiguracja bazy danych
		$this->db = new \Mmi\Db\Config();
		//ustawienia routera
		$this->router = new \Mmi\Mvc\Router\Config();
		//konfiguracja sesji
		$this->session = new \Mmi\Session\Config();
	}

}
