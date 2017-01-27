<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
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
	public function __construct() {
		//inicjalizacja tłumaczeń
		$translate = $this->_setupTranslate();

		//ustawienie front controllera, sesji i bazy danych
		$this
			->_setupDatabase()
			->_setupCache()
			->_setupFrontController($router = $this->_setupRouter($translate->getLocale()), $this->_setupView($translate, $router))
			->_setupSession();
	}

	/**
	 * Uruchomienie bootstrapa skutkuje uruchomieniem front controllera
	 */
	public function run() {
		\Mmi\App\FrontController::getInstance()->run();
	}

	/**
	 * Inicjalizacja routera
	 * @param string $language
	 * @return \Mmi\Mvc\Router
	 */
	protected function _setupRouter($language) {
		return new \Mmi\Mvc\Router(\App\Registry::$config->router, $language);
	}

	/**
	 * Inicjalizacja tłumaczeń
	 * @return \Mmi\Translate
	 */
	protected function _setupTranslate() {
		$translate = new \Mmi\Translate;
		//domyślny język
		$translate->setDefaultLocale(isset(\App\Registry::$config->languages[0]) ? \App\Registry::$config->languages[0] : null);
		//język ze zmiennej środowiskowej
		$envLang = \Mmi\App\FrontController::getInstance()->getEnvironment()->applicationLanguage;
		if (null === $envLang) {
			//zwrot translate z domyślnym locale
			return $translate;
		}
		//brak języka ze zmiennej środowiskowej
		if (!in_array($envLang, \App\Registry::$config->languages)) {
			return $translate;
		}
		//zwrot translate z ustawieniem locale
		return $translate->setLocale($envLang);
	}

	/**
	 * Inicjalizacja sesji
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupSession() {
		//brak sesji
		if (!\App\Registry::$config->session->name) {
			return $this;
		}
		//własna sesja, oparta na obiekcie implementującym SessionHandlerInterface
		if (strtolower(\App\Registry::$config->session->handler) == 'user') {
			$sessionClass = \App\Registry::$config->session->path;
			session_set_save_handler(new $sessionClass);
		}
		//uruchomienie sesji
		\Mmi\Session\Session::start(\App\Registry::$config->session);
		return $this;
	}

	/**
	 * Inicjalizacja bufora
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupCache() {
		\App\Registry::$cache = new \Mmi\Cache\Cache(\App\Registry::$config->cache);
		//wstrzyknięcie cache do ORM
		\Mmi\Orm\DbConnector::setCache(\App\Registry::$cache);
		return $this;
	}

	/**
	 * Ustawianie bazy danych
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupDatabase() {
		//połączenie do bazy danych i konfiguracja DAO
		if (\App\Registry::$config->db->driver === null) {
			return $this;
		}
		//uzupełnienie rejestru
		\App\Registry::$db = \Mmi\Db\DbHelper::getAdapter(\App\Registry::$config->db);
		//wstrzyknięcie do ORM
		\Mmi\Orm\DbConnector::setAdapter(\App\Registry::$db);
		return $this;
	}

	/**
	 * Ustawianie front controllera
	 * @param \Mmi\Mvc\Router $router
	 * @param \Mmi\Mvc\View $view
	 * @return \Mmi\App\Bootstrap
	 */
	protected function _setupFrontController(\Mmi\Mvc\Router $router, \Mmi\Mvc\View $view) {
		//wczytywanie struktury frontu z cache
		if (null === ($frontStructure = \App\Registry::$cache->load($cacheKey = 'mmi-structure'))) {
			\App\Registry::$cache->save($frontStructure = \Mmi\Mvc\Structure::getStructure(), $cacheKey, 0);
		}
		//inicjalizacja frontu
		$frontController = \Mmi\App\FrontController::getInstance();
		//konfiguracja frontu
		$frontController->setStructure($frontStructure)
			->setRouter($router)
			->setView($view)
			->getResponse()->setDebug(\App\Registry::$config->debug);
		//rejestracja pluginów
		foreach (\App\Registry::$config->plugins as $plugin) {
			$frontController->registerPlugin(new $plugin());
		}
		return $this;
	}

	/**
	 * Inicjalizacja widoku
	 * @param \Mmi\Translate $translate
	 * @param \Mmi\Mvc\Router $router
	 * @return \Mmi\Mvc\View
	 */
	protected function _setupView(\Mmi\Translate $translate, \Mmi\Mvc\Router $router) {
		//powołanie widoku
		$view = new \Mmi\Mvc\View;
		//ustawienie widoku
		return $view->setCache(\App\Registry::$cache)
				->setAlwaysCompile(\App\Registry::$config->compile)
				->setTranslate($translate)
				->setBaseUrl($router->getBaseUrl());
	}

}
