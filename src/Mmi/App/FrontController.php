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
	 * @var \Mmi\Controller\Request
	 */
	private $_request;

	/**
	 * Response (odpowiedź)
	 * @var \Mmi\Controller\Response
	 */
	private $_response;

	/**
	 * Router
	 * @var \Mmi\Controller\Router
	 */
	private $_router;

	/**
	 * Środowisko uruchomieniowe
	 * @var \Mmi\Controller\Environment
	 */
	private $_environment;

	/**
	 * Widok
	 * @var \Mmi\View
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
		$this->_request = new \Mmi\Controller\Request();
		//nowy odpowiedź
		$this->_response = new \Mmi\Controller\Response();
		//nowe środowisko
		$this->_environment = new \Mmi\Controller\Environment();
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
	 * @param \Mmi\Controller\Request $request
	 * @return \Mmi\App\FrontController
	 */
	public function setRequest(\Mmi\Controller\Request $request) {
		$this->_request = $request;
		return $this;
	}

	/**
	 * Ustawienie odpowiedzi
	 * @param \Mmi\Controller\Response $response
	 * @return \Mmi\App\FrontController
	 */
	public function setResponse(\Mmi\Controller\Response $response) {
		$this->_response = $response;
		return $this;
	}

	/**
	 * Ustawia router
	 * @param \Mmi\Controller\Router $router
	 * @return \Mmi\App\FrontController
	 */
	public function setRouter(\Mmi\Controller\Router $router) {
		$this->_router = $router;
		return $this;
	}

	/**
	 * Ustawia widok
	 * @param \Mmi\View $view
	 * @return \Mmi\App\FrontController
	 */
	public function setView(\Mmi\View $view) {
		$this->_view = $view;
		return $this;
	}

	/**
	 * Pobranie żądania
	 * @return \Mmi\Controller\Request
	 */
	public function getRequest() {
		return $this->_request;
	}

	/**
	 * Pobranie odpowiedzi
	 * @return \Mmi\Controller\Response
	 */
	public function getResponse() {
		return $this->_response;
	}

	/**
	 * Pobranie routera
	 * @return \Mmi\Controller\Router
	 */
	public function getRouter() {
		//brak routera
		if ($this->_router === null) {
			throw new \Exception('\Mmi\App\FrontController: no router specified');
		}
		return $this->_router;
	}

	/**
	 * Pobiera środowisko uruchomieniowe
	 * @return \Mmi\Controller\Environment
	 */
	public function getEnvironment() {
		return $this->_environment;
	}

	/**
	 * Pobranie widoku
	 * @return \Mmi\View
	 */
	public function getView() {
		//brak widoku
		if ($this->_view === null) {
			throw new \Exception('\Mmi\App\FrontController: no view specified');
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
			throw new \Exception('\Mmi\Contoller\Front structure not found');
		}
		//struktura nieprawidłowa (brak części)
		if ($part !== null && !isset($this->_structure[$part])) {
			throw new \Exception('\Mmi\App\FrontController structure invalid');
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
		\Mmi\Profiler::event('Front Controller: plugins route startup');

		//stosowanie routingu jeśli request jest pusty
		if (!$this->_request->getModuleName()) {
			$this->getRouter()->processRequest($this->_request);
		}

		//new relic
		extension_loaded('newrelic') ? newrelic_name_transaction($this->_request->module . '/' . $this->_request->controller . '/' . $this->_request->action) : null;

		\Mmi\Profiler::event('Front Controller: routing applied');

		//wpięcie dla pluginów przed dispatchem
		$this->preDispatch();
		\Mmi\Profiler::event('Front Controller: plugins pre-dispatch');
		//wybór i uruchomienie kontrolera akcji
		$content = \Mmi\Controller\ActionPerformer::getInstance()->action($this->getRequest()->toArray());

		//wpięcie dla pluginów po dispatchu
		$this->postDispatch();
		\Mmi\Profiler::event('Front Controller: plugins post-dispatch');

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
