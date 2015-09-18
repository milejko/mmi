<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\App;

class Bootstrap implements BootstrapInterface {

	/**
	 * Konstruktor, ustawia ścieżki, ładuje domyślne klasy, ustawia autoloadera
	 */
	public function __construct() {
		//inicjalizacja konfiguracji aplikacji
		$config = $this->_initConfiguration();

		//ustawienie cache
		$this->_setupCache($config);

		//inicjalizacja tłumaczeń
		$translate = $this->_initTranslate($config);

		//inicjalizacja routera
		$router = $this->_initRouter($config, $translate->getLocale());

		//inicjalizacja widoku
		$view = $this->_initView($config, $translate, $router);

		//ustawienie front controllera
		$this->_setupFrontController($config, $router, $view);

		//ustawienie bazy danych
		$this->_setupDatabase($config);
	}

	/**
	 * Uruchomienie bootstrapa skutkuje uruchomieniem front controllera
	 */
	public function run() {
		\Mmi\Controller\Front::getInstance()->dispatch();
	}

	/**
	 * Ładowanie konfiguracji
	 * @return \Mmi\App\Config\App
	 * @throws Exception
	 */
	protected function _initConfiguration() {
		//lokalna konfiguracja
		$config = new \App\Config\Local();

		//konfiguracja profilera aplikacji
		\Mmi\Profiler::setEnabled($config->debug);

		//ustawienie lokalizacji
		date_default_timezone_set($config->timeZone);
		ini_set('default_charset', $config->charset);
		return $config;
	}

	/**
	 * Ustawianie bufora
	 * @throws Exception
	 */
	protected function _setupCache(\Mmi\App\Config\App $config) {
		\App\Registry::$config = $config;
		\App\Registry::$cache = new \Mmi\Cache($config->cache);
	}

	/**
	 * Inicjalizacja routera
	 * @param \Mmi\App\Config\App $config
	 * @param string $language
	 * @return \Mmi\Controller\Router
	 */
	protected function _initRouter(\Mmi\App\Config\App $config, $language) {
		return new \Mmi\Controller\Router($config->router, $language);
	}

	/**
	 * Inicjalizacja tłumaczeń
	 * @param \Mmi\App\Config\App $config
	 * @return \Mmi\Translate
	 */
	protected function _initTranslate(\Mmi\App\Config\App $config) {
		$defaultLanguage = isset($config->languages[0]) ? $config->languages[0] : null;
		$translate = new \Mmi\Translate();
		$translate->setDefaultLocale($defaultLanguage);
		$envLang = \Mmi\Controller\Front::getInstance()->getEnvironment()->applicationLanguage;
		if (null === $envLang) {
			return $translate;
		}
		if (!in_array($envLang, $config->languages)) {
			return $translate;
		}
		$translate->setLocale($envLang);
		return $translate;
	}

	/**
	 * Ustawianie bazy danych
	 * @param \Mmi\App\Config\App $config
	 */
	protected function _setupDatabase(\Mmi\App\Config\App $config) {
		//połączenie do bazy danych i konfiguracja DAO
		if (\App\Registry::$config->db->driver === null) {
			return;
		}
		\App\Registry::$config->db->profiler = $config->debug;
		\App\Registry::$db = \Mmi\Db::factory(\App\Registry::$config->db);
		\Mmi\Orm::setAdapter(\App\Registry::$db);
		\Mmi\Orm::setCache(\App\Registry::$cache);
	}

	/**
	 * Ustawianie front controllera
	 * @param \Mmi\App\Config\App $config
	 * @param \Mmi\Controller\Router $router
	 * @param \Mmi\View $view
	 */
	protected function _setupFrontController(\Mmi\App\Config\App $config, \Mmi\Controller\Router $router, \Mmi\View $view) {
		//wczytywanie struktury frontu z cache
		if (null === ($frontStructure = \App\Registry::$cache->load('Mmi-Structure'))) {
			\App\Registry::$cache->save($frontStructure = \Mmi\App\Structure::getStructure(), 'Mmi-Structure', 0);
		}
		//inicjalizacja frontu
		$frontController = \Mmi\Controller\Front::getInstance();
		$frontController->setStructure($frontStructure)
			->setRouter($router)
			->setView($view)
			->getResponse()->setDebug($config->debug);
		//rejestracja pluginów
		foreach ($config->plugins as $plugin) {
			$frontController->registerPlugin(new $plugin());
		}
	}

	/**
	 * Inicjalizacja widoku
	 * @param \Mmi\App\Config\App $config
	 * @param \Mmi\Translate $translate
	 * @param \Mmi\Controller\Router $router
	 * @return \Mmi\View
	 */
	protected function _initView(\Mmi\App\Config\App $config, \Mmi\Translate $translate, \Mmi\Controller\Router $router) {
		//konfiguracja widoku
		$view = new \Mmi\View();
		$view->setCache(\App\Registry::$cache)
			->setAlwaysCompile($config->compile)
			->setTranslate($translate)
			->setBaseUrl($router->getBaseUrl());
		return $view;
	}

}
