<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App\Config;

/**
 * Klasa konfiguracji aplikacji
 */
abstract class App {

	/**
	 * Podstawowa konfiguracja aplikacji
	 * @var \Mmi\App\Config\App
	 */
	public $application;

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

	/*
	 * Nawigacja
	 * @var \Mmi\Navigation\Config
	 */
	public $navigation;

	/**
	 * Konfiguracja routera
	 * @var \Mmi\Controller\Router\Config
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
	public $debug = true;

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

	public function __construct() {

		$this->cache = new \Mmi\Cache\Config();
		$this->db = new \Mmi\Db\Config();
		$this->navigation = new \Mmi\Navigation\Config();
		$this->router = new \Mmi\Controller\Router\Config();
		$this->session = new \Mmi\Session\Config();
	}

}
