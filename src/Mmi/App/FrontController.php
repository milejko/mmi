<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

/**
 * Klasa implementująca kontroler frontu
 */
class FrontController
{

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
     * @var \Mmi\App\KernelProfilerInterface
     */
    private $_profiler;

    /**
     * Lokalny bufor aplikacji
     * @var \Mmi\Cache\Cache
     */
    private $_cache;

    /**
     * Logger PSR
     * @var \Psr\Log\LoggerInterface
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
    protected function __construct()
    {
        //nowe zapytanie
        $this->_request = new \Mmi\Http\Request;
        //nowy odpowiedź
        $this->_response = new \Mmi\Http\Response;
        //nowe środowisko
        $this->_environment = new \Mmi\Http\HttpServerEnv;
        //logger
        $this->_logger = \Mmi\Log\LoggerHelper::getLogger();
    }

    /**
     * Pobranie instancji
     * @return \Mmi\App\FrontController
     */
    public static function getInstance($fresh = false)
    {
        //zwrot instancji, lub utworzenie nowej
        return (self::$_instance && !$fresh) ? self::$_instance : (self::$_instance = new self);
    }

    /**
     * Dodanie pluginu
     * @param \Mmi\App\FrontControllerPluginAbstract $plugin
     * @return \Mmi\App\FrontController
     */
    public function registerPlugin(\Mmi\App\FrontControllerPluginAbstract $plugin)
    {
        //dodawanie pluginu
        $this->_plugins[] = $plugin;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawia strukturę frontu
     * @param array $structure
     * @return \Mmi\App\FrontController
     */
    public function setStructure(array $structure = [])
    {
        //ustawianie struktury
        $this->_structure = $structure;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawienie żądania
     * @param \Mmi\Http\Request $request
     * @return \Mmi\App\FrontController
     */
    public function setRequest(\Mmi\Http\Request $request)
    {
        //ustawianie requestu
        $this->_request = $request;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawia profiler
     * @param \Mmi\App\KernelProfilerInterface $profiler
     * @return \Mmi\App\FrontController
     */
    public function setProfiler(\Mmi\App\KernelProfilerInterface $profiler)
    {
        //ustawianie profilera
        $this->_profiler = $profiler;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawienie odpowiedzi
     * @param \Mmi\Http\Response $response
     * @return \Mmi\App\FrontController
     */
    public function setResponse(\Mmi\Http\Response $response)
    {
        //ustawianie odpowiedzi
        $this->_response = $response;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawia router
     * @param \Mmi\Mvc\Router $router
     * @return \Mmi\App\FrontController
     */
    public function setRouter(\Mmi\Mvc\Router $router)
    {
        //ustawienie routera
        $this->_router = $router;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawia lokalny bufor
     * @param \Mmi\Cache\Cache $cache
     * @return \Mmi\App\FrontController
     */
    public function setLocalCache(\Mmi\Cache\Cache $cache)
    {
        //ustawianie lokalnego bufora
        $this->_cache = $cache;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawia widok
     * @param \Mmi\Mvc\View $view
     * @return \Mmi\App\FrontController
     */
    public function setView(\Mmi\Mvc\View $view)
    {
        //ustawianie widoku
        $this->_view = $view;
        //zwrot siebie
        return $this;
    }

    /**
     * Pobranie żądania
     * @return \Mmi\Http\Request
     */
    public function getRequest()
    {
        //pobiera żądanie
        return $this->_request;
    }

    /**
     * Pobranie odpowiedzi
     * @return \Mmi\Http\Response
     */
    public function getResponse()
    {
        //pobieranie odpowiedzi
        return $this->_response;
    }

    /**
     * Pobranie routera
     * @return \Mmi\Mvc\Router
     */
    public function getRouter()
    {
        //brak routera
        if (!$this->_router) {
            throw new KernelException('\Mmi\Mvc\Router should be specified in \Mmi\App\FrontController');
        }
        //zwrot routera
        return $this->_router;
    }

    /**
     * Pobiera środowisko uruchomieniowe
     * @return \Mmi\Http\HttpServerEnv
     */
    public function getEnvironment()
    {
        //zwrot obiektu środowiskowego
        return $this->_environment;
    }

    /**
     * Zwraca zarejestrowane pluginy
     * @return array
     */
    public function getPlugins()
    {
        //zwrot pluginów
        return $this->_plugins;
    }

    /**
     * Zwraca profiler
     * @return \Mmi\App\KernelProfiler
     */
    public function getProfiler()
    {
        //zwrot profilera lub tworzenie nowego, lekkiego profilera
        return $this->_profiler ? $this->_profiler : ($this->_profiler = new NullKernelProfiler);
    }

    /**
     * Zwraca logger
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger()
    {
        //zwraca logger
        return $this->_logger;
    }

    /**
     * Pobiera lokalny bufor
     * @return \Mmi\Cache\Cache
     */
    public function getLocalCache()
    {
        //brak bufora
        if (!$this->_cache) {
            throw new KernelException('\Mmi\Cache\Cache should be specified as localCache in \Mmi\App\FrontController');
        }
        //zwrot lokalnego bufora
        return $this->_cache;
    }

    /**
     * Pobranie widoku
     * @return \Mmi\Mvc\View
     */
    public function getView()
    {
        //brak widoku
        if (!$this->_view) {
            throw new KernelException('\Mmi\Mvc\View should be specified in \Mmi\App\FrontController');
        }
        //zwrot widoku
        return $this->_view;
    }

    /**
     * Pobiera strukturę aplikacji
     * @param string $part opcjonalnie można pobrać jedynie 'module'
     * @return array
     */
    public function getStructure($part = null)
    {
        //brak struktury
        if (!$this->_structure) {
            throw new KernelException('\Mmi\App\FrontController: structure not found');
        }
        //pobranie całej struktury
        if (!$part) {
            return $this->_structure;
        }
        //struktura nieprawidłowa (brak części)
        if (!isset($this->_structure[$part])) {
            throw new KernelException('\Mmi\App\FrontController: structure invalid');
        }
        //zwrot części struktury
        return $this->_structure[$part];
    }

    /**
     * Uruchamianie
     * @return \Mmi\Http\Response
     */
    public function run()
    {
        //pobranie odpowiedzi
        return (new \Mmi\Mvc\Dispatcher)->dispatch();
    }

}
