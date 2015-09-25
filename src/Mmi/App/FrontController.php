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
 * Klasa implementująca kontroler frontu
 */
class FrontController {

	/**
	 * Instancja front kontrolera
	 * @var \Mmi\App\FrontController
	 */
	private static $_instance;

	/**
	 * Request (żądanie)
	 * @var \Mmi\Http\Request
	 */
	private $_request;

	/**
	 * Response (odpowiedź)
	 * @var \Mmi\Http\Response
	 */
	private $_response;

	/**
	 * Router
	 * @var \Mmi\Mvc\Router
	 */
	private $_router;

	/**
	 * Środowisko uruchomieniowe
	 * @var \Mmi\App\Environment
	 */
	private $_environment;

	/**
	 * Widok
	 * @var \Mmi\Mvc\View
	 */
	private $_view;

	/**
	 * Lista pluginów
	 * @var array
	 */
	private $_plugins = [];

	/**
	 * Struktura aplikacji
	 * @var array
	 */
	private $_structure;

	/**
	 * Zabezpieczony konstruktor
	 */
	protected function __construct() {
		//nowe zapytanie
		$this->_request = new \Mmi\Http\Request();
		//nowy odpowiedź
		$this->_response = new \Mmi\Http\Response();
		//nowe środowisko
		$this->_environment = new \Mmi\App\Environment();
	}

	/**
	 * Pobranie instancji
	 * @return \Mmi\App\FrontController
	 */
	public static function getInstance() {
		//jeśli nie istnieje instancja tworzenie nowej
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		//zwrot instancji
		return self::$_instance;
	}

	/**
	 * Ustawia strukturę frontu
	 * @param array $structure
	 * @return \Mmi\App\FrontController
	 */
	public function setStructure(array $structure = []) {
		$this->_structure = $structure;
		return $this;
	}

	/**
	 * Dodanie pluginu
	 * @param \Mmi\App\FrontControllerPluginAbstract $plugin
	 * @return \Mmi\App\FrontController
	 */
	public function registerPlugin(\Mmi\App\FrontControllerPluginAbstract $plugin) {
		$this->_plugins[] = $plugin;
		return $this;
	}

	/**
	 * Ustawienie żądania
	 * @param \Mmi\Http\Request $request
	 * @return \Mmi\App\FrontController
	 */
	public function setRequest(\Mmi\Http\Request $request) {
		$this->_request = $request;
		return $this;
	}

	/**
	 * Ustawienie odpowiedzi
	 * @param \Mmi\Http\Response $response
	 * @return \Mmi\App\FrontController
	 */
	public function setResponse(\Mmi\Http\Response $response) {
		$this->_response = $response;
		return $this;
	}

	/**
	 * Ustawia router
	 * @param \Mmi\Mvc\Router $router
	 * @return \Mmi\App\FrontController
	 */
	public function setRouter(\Mmi\Mvc\Router $router) {
		$this->_router = $router;
		return $this;
	}

	/**
	 * Ustawia widok
	 * @param \Mmi\Mvc\View $view
	 * @return \Mmi\App\FrontController
	 */
	public function setView(\Mmi\Mvc\View $view) {
		$this->_view = $view;
		return $this;
	}

	/**
	 * Pobranie żądania
	 * @return \Mmi\Http\Request
	 */
	public function getRequest() {
		return $this->_request;
	}

	/**
	 * Pobranie odpowiedzi
	 * @return \Mmi\Http\Response
	 */
	public function getResponse() {
		return $this->_response;
	}

	/**
	 * Pobranie routera
	 * @return \Mmi\Mvc\Router
	 */
	public function getRouter() {
		//brak routera
		if ($this->_router === null) {
			throw new Exception('\Mmi\Mvc\Router should be specified in \Mmi\App\FrontController');
		}
		return $this->_router;
	}

	/**
	 * Pobiera środowisko uruchomieniowe
	 * @return \Mmi\App\Environment
	 */
	public function getEnvironment() {
		return $this->_environment;
	}

	/**
	 * Pobranie widoku
	 * @return \Mmi\Mvc\View
	 */
	public function getView() {
		//brak widoku
		if ($this->_view === null) {
			throw new Exception('\Mmi\View should be specified in \Mmi\App\FrontController');
		}
		return $this->_view;
	}

	/**
	 * Pobiera strukturę aplikacji
	 * @param string $part opcjonalnie można pobrać jedynie 'module'
	 * @return array
	 */
	public function getStructure($part = null) {
		//brak struktury
		if ($this->_structure === null) {
			throw new Exception('\Mmi\Contoller\Front structure not found');
		}
		//struktura nieprawidłowa (brak części)
		if ($part !== null && !isset($this->_structure[$part])) {
			throw new Exception('\Mmi\App\FrontController structure invalid');
		}
		return (null === $part) ? $this->_structure : $this->_structure[$part];
	}

	/**
	 * Uruchamianie metody routeStartup na zarejestrowanych pluginach
	 */
	public function routeStartup() {
		foreach ($this->_plugins as $plugin) {
			//wykonywanie routeStartup() na kolejnych pluginach
			$plugin->routeStartup($this->_request);
		}
	}

	/**
	 * Uruchamianie metody preDispatch na zarejestrowanych pluginach
	 */
	public function preDispatch() {
		foreach ($this->_plugins as $plugin) {
			//wykonywanie preDispatch() na kolejnych pluginach
			$plugin->preDispatch($this->_request);
		}
	}

	/**
	 * Uruchamianie metody postDispatch na zarejestrowanych pluginach
	 */
	public function postDispatch() {
		foreach ($this->_plugins as $plugin) {
			//wykonywanie postDispatch() na kolejnych pluginach
			$plugin->postDispatch($this->_request);
		}
	}

	/**
	 * Dispatcher
	 */
	public function dispatch() {
		//wpięcie dla pluginów przed routingiem
		$this->routeStartup();
		\Mmi\App\Profiler::event('Front Controller: plugins route startup');

		//stosowanie routingu jeśli request jest pusty
		if (!$this->_request->getModuleName()) {
			$this->getRouter()->processRequest($this->_request);
		}

		//new relic
		extension_loaded('newrelic') ? newrelic_name_transaction($this->_request->module . '/' . $this->_request->controller . '/' . $this->_request->action) : null;

		\Mmi\App\Profiler::event('Front Controller: routing applied');

		//wpięcie dla pluginów przed dispatchem
		$this->preDispatch();
		\Mmi\App\Profiler::event('Front Controller: plugins pre-dispatch');
		//wybór i uruchomienie kontrolera akcji
		$content = \Mmi\Mvc\ActionPerformer::getInstance()->action($this->getRequest()->toArray());

		//wpięcie dla pluginów po dispatchu
		$this->postDispatch();
		\Mmi\App\Profiler::event('Front Controller: plugins post-dispatch');

		//jeśli layout nie jest wyłączony
		if (!$this->getView()->isLayoutDisabled()) {
			//renderowanie layoutu
			$content = $this->getView()
				->setRequest($this->_request)
				->setPlaceholder('content', $content)
				->renderLayout($this->_request->__get('module'), $this->_request->__get('controller'));
		}

		//wysłanie odpowiedzi
		$this->getResponse()
			->setContent($content)
			->send();
	}

}
