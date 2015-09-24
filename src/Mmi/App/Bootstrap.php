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
 * Klasa rozruchu aplikacji
 */
class Bootstrap implements BootstrapInterface {

	/**
	 * Konstruktor, ustawia ścieżki, ładuje domyślne klasy, ustawia autoloadera
	 */
	public function __construct($env) {
		//inicjalizacja konfiguracji aplikacji
		$config = $this->_setupConfiguration($env);

		//ustawienie cache
		$this->_setupCache($config);

		//inicjalizacja tłumaczeń
		$translate = $this->_setupTranslate($config);

		//inicjalizacja routera
		$router = $this->_setupRouter($config, $translate->getLocale());

		//inicjalizacja widoku
		$view = $this->_setupView($config, $translate, $router);

		//ustawienie front controllera, sesji i bazy danych
		$this->_setupFrontController($config, $router, $view)
			->_setupSession($config)
			->_setupDatabase($config);
	}

	/**
	 * Uruchomienie bootstrapa skutkuje uruchomieniem front controllera
	 */
	public function run() {
		\Mmi\App\FrontController::getInstance()->dispatch();
	}

	/**
	 * Ładowanie konfiguracji
	 * @return \Mmi\App\KernelConfig
	 */
	protected function _setupConfiguration($env) {
		//konwencja nazwy konfiguracji
		$configClassName = '\App\KernelConfig' . $env;
		//konfiguracja dla danego środowiska
		$config = new $configClassName();
		//konfiguracja profilera aplikacji
		\Mmi\Profiler::setEnabled($config->debug);
		//ustawienie lokalizacji
		date_default_timezone_set($config->timeZone);
		ini_set('default_charset', $config->charset);
		return $config;
	}

	/**
	 * Ustawianie bufora
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupCache(\Mmi\App\KernelConfig $config) {
		\App\Registry::$config = $config;
		\App\Registry::$cache = new \Mmi\Cache($config->cache);
		return $this;
	}

	/**
	 * Inicjalizacja routera
	 * @param \Mmi\App\KernelConfig $config
	 * @param string $language
	 * @return \Mmi\Controller\Router
	 */
	protected function _setupRouter(\Mmi\App\KernelConfig $config, $language) {
		return new \Mmi\Controller\Router($config->router, $language);
	}

	/**
	 * Inicjalizacja tłumaczeń
	 * @param \Mmi\App\KernelConfig $config
	 * @return \Mmi\Translate
	 */
	protected function _setupTranslate(\Mmi\App\KernelConfig $config) {
		$defaultLanguage = isset($config->languages[0]) ? $config->languages[0] : null;
		$translate = new \Mmi\Translate();
		$translate->setDefaultLocale($defaultLanguage);
		$envLang = \Mmi\App\FrontController::getInstance()->getEnvironment()->applicationLanguage;
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
	 * Inicjalizacja sesji
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupSession(\Mmi\App\KernelConfig $config) {
		//ustawianie sesji
		if (!$config->session->name) {
			return $this;
		}
		//ustawia ID sesji jeśli jawnie podana w żądaniu get
		(null !== ($sid = filter_input(INPUT_GET, 'sessionId', FILTER_DEFAULT))) ? \Mmi\Session::setId($sid) : null;
		\Mmi\Session::start($config->session);
		return $this;
	}

	/**
	 * Ustawianie bazy danych
	 * @param \Mmi\App\KernelConfig $config
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupDatabase(\Mmi\App\KernelConfig $config) {
		//połączenie do bazy danych i konfiguracja DAO
		if ($config->db->driver === null) {
			return $this;
		}
		//ustawienie profilera
		$config->db->profiler = $config->debug;
		//uzupełnienie rejestru
		\App\Registry::$db = \Mmi\Db\Component::factory($config->db);
		//wstrzyknięcie do ORM
		\Mmi\Orm\DbConnector::setAdapter(\App\Registry::$db);
		\Mmi\Orm\DbConnector::setCache(\App\Registry::$cache);
		return $this;
	}

	/**
	 * Ustawianie front controllera
	 * @param \Mmi\App\KernelConfig $config
	 * @param \Mmi\Controller\Router $router
	 * @param \Mmi\Mvc\View $view
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupFrontController(\Mmi\App\KernelConfig $config, \Mmi\Controller\Router $router, \Mmi\Mvc\View $view) {
		//wczytywanie struktury frontu z cache
		if (null === ($frontStructure = \App\Registry::$cache->load('Mmi-Structure'))) {
			\App\Registry::$cache->save($frontStructure = \Mmi\App\Structure::getStructure(), 'Mmi-Structure', 0);
		}
		//inicjalizacja frontu
		$frontController = \Mmi\App\FrontController::getInstance();
		$frontController->setStructure($frontStructure)
			->setRouter($router)
			->setView($view)
			->getResponse()->setDebug($config->debug);
		//rejestracja pluginów
		foreach ($config->plugins as $plugin) {
			$frontController->registerPlugin(new $plugin());
		}
		return $this;
	}

	/**
	 * Inicjalizacja widoku
	 * @param \Mmi\App\KernelConfig $config
	 * @param \Mmi\Translate $translate
	 * @param \Mmi\Controller\Router $router
	 * @return \Mmi\Mvc\View
	 */
	protected function _setupView(\Mmi\App\KernelConfig $config, \Mmi\Translate $translate, \Mmi\Controller\Router $router) {
		//konfiguracja widoku
		$view = new \Mmi\Mvc\View();
		$view->setCache(\App\Registry::$cache)
			->setAlwaysCompile($config->compile)
			->setTranslate($translate)
			->setBaseUrl($router->getBaseUrl());
		return $view;
	}

}
