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
	 * @var \Mmi\Http\HttpServerEnv
	 */
	private $_environment;

	/**
	 * Profiler aplikacji
	 * @var \Mmi\App\KernelProfiler 
	 */
	private $_profiler;

	/**
	 * Lokalny bufor aplikacji
	 * @var \Mmi\Cache\Cache
	 */
	private $_cache;

	/**
	 * Logger - monolog
	 * @var \Monolog\Logger
	 */
	private $_logger;

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
		$this->_request = new \Mmi\Http\Request;
		//nowy odpowiedź
		$this->_response = new \Mmi\Http\Response;
		//nowe środowisko
		$this->_environment = new \Mmi\Http\HttpServerEnv;
		//profiler aplikacji
		$this->_profiler = new \Mmi\App\KernelProfiler;
		//logger - monolog
		$this->_logger = \Mmi\Log\LoggerHelper::getLogger();
	}

	/**
	 * Pobranie instancji
	 * @return \Mmi\App\FrontController
	 */
	public static function getInstance() {
		//jeśli nie istnieje instancja tworzenie nowej
		if (null === self::$_instance) {
			self::$_instance = new self;
		}
		//zwrot instancji
		return self::$_instance;
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
	 * Ustawia strukturę frontu
	 * @param array $structure
	 * @return \Mmi\App\FrontController
	 */
	public function setStructure(array $structure = []) {
		$this->_structure = $structure;
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
	 * Ustawia lokalny bufor
	 * @param \Mmi\Cache\Cache $cache
	 * @return \Mmi\App\FrontController
	 */
	public function setLocalCache(\Mmi\Cache\Cache $cache) {
		$this->_cache = $cache;
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
			throw new KernelException('\Mmi\Mvc\Router should be specified in \Mmi\App\FrontController');
		}
		return $this->_router;
	}

	/**
	 * Pobiera środowisko uruchomieniowe
	 * @return \Mmi\Http\HttpServerEnv
	 */
	public function getEnvironment() {
		return $this->_environment;
	}

	/**
	 * Zwraca zarejestrowane pluginy
	 * @return array
	 */
	public function getPlugins() {
		return $this->_plugins;
	}

	/**
	 * Zwraca profiler
	 * @return \Mmi\App\KernelProfiler
	 */
	public function getProfiler() {
		return $this->_profiler;
	}

	/**
	 * Zwraca logger
	 * @return \Monolog\Logger
	 */
	public function getLogger() {
		return $this->_logger;
	}

	/**
	 * Pobiera lokalny bufor
	 * @return \Mmi\Cache\Cache
	 */
	public function getLocalCache() {
		//brak bufora
		if (null === $this->_cache) {
			throw new KernelException('Missing localCache configuration section');
		}
		return $this->_cache;
	}

	/**
	 * Pobranie widoku
	 * @return \Mmi\Mvc\View
	 */
	public function getView() {
		//brak widoku
		if ($this->_view === null) {
			throw new KernelException('\Mmi\Mvc\View should be specified in \Mmi\App\FrontController');
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
			throw new KernelException('\Mmi\App\FrontController structure not found');
		}
		//struktura nieprawidłowa (brak części)
		if ($part !== null && !isset($this->_structure[$part])) {
			throw new KernelException('\Mmi\App\FrontController structure invalid');
		}
		return (null === $part) ? $this->_structure : $this->_structure[$part];
	}

	/**
	 * Uruchamianie
	 */
	public function run() {
		//dispatcher
		$content = (new \Mmi\Mvc\Dispatcher)->dispatch();
		//jeśli layout nie jest wyłączony
		if (!$this->getView()->isLayoutDisabled()) {
			//renderowanie layoutu
			$content = $this->getView()
				->setPlaceholder('content', $content)
				->renderLayout($this->getRequest());
		}
		//wysłanie odpowiedzi
		$this->getResponse()
			->setContent($content)
			->send();
	}

}
