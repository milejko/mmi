<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Klasa konfiguracji aplikacji
 */
abstract class Config {

	/**
	 * Podstawowa konfiguracja aplikacji
	 * @var \Mmi\Application\Config
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

	public function __construct() {

		$this->application = new \Mmi\Application\Config();
		$this->cache = new \Mmi\Cache\Config();
		$this->db = new \Mmi\Db\Config();
		$this->navigation = new \Mmi\Navigation\Config();
		$this->router = new \Mmi\Controller\Router\Config();
		$this->session = new \Mmi\Session\Config();
	}

}
