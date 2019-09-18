<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Mmi\Http\ResponseTimingHeader;

/**
 * Klasa rozruchu aplikacji
 */
class Bootstrap implements BootstrapInterface
{

    const KERNEL_PROFILER_PREFIX = 'App\Bootstrap';

    /**
     * Konstruktor, ustawia ścieżki, ładuje domyślne klasy, ustawia autoloadera
     */
    public function __construct()
    {
        //ustawienie front controllera, sesji i bazy danych
        $this->_setupDatabase()
            //konfiguracja lokalnego bufora
            ->_setupLocalCache()
            //konfiguracja front controllera
            ->_setupFrontController($router = $this->_setupRouter(), $this->_setupView($router))
            //konfiguracja cache
            ->_setupCache()
            //konfiguracja tłumaczeń
            ->_setupTranslate()
            //konfiguracja lokalizacji
            ->_setupLocale()
            //konfiguracja sesji
            ->_setupSession();
    }

    /**
     * Uruchomienie bootstrapa skutkuje uruchomieniem front controllera
     */
    public function run()
    {
        //uruchomienie front controllera
        FrontController::getInstance()->run();
        //wysyłka nagółwka Server-Timing
        (new ResponseTimingHeader(FrontController::getInstance()->getProfiler()))->getTimingHeader()->send();
    }

    /**
     * Inicjalizacja routera
     * @param string $language
     * @return \Mmi\Mvc\Router
     */
    protected function _setupRouter()
    {
        //powołanie routera z konfiguracją
        return new \Mmi\Mvc\Router(\App\Registry::$config->router ? \App\Registry::$config->router : new \Mmi\Mvc\RouterConfig);
    }

    /**
     * Inicjalizacja lokalizacji
     * @return \Mmi\App\Bootstrap
     */
    protected function _setupLocale()
    {
        //getting language from environment
        if (null === FrontController::getInstance()->getEnvironment()->lang) {
            //zwrot translate z domyślnym locale
            return $this;
        }
        //brak języka ze zmiennej środowiskowej
        if (!in_array(FrontController::getInstance()->getEnvironment()->lang, \App\Registry::$config->languages)) {
            return $this;
        }
        //ustawianie locale ze środowiska
        \App\Registry::$translate->setLocale(FrontController::getInstance()->getEnvironment()->lang);
        return $this;
    }

    protected function _setupTranslate()
    {
        //pobranie struktury translatora
        $structure = FrontController::getInstance()->getStructure('translate');
        //ładowanie zbuforowanego translatora
        $cache = FrontController::getInstance()->getLocalCache();
        //klucz buforowania
        $key = 'mmi-translate';
        //próba załadowania z bufora
        if ($cache !== null && (null !== ($cachedTranslate = $cache->load($key)))) {
            //wczytanie obiektu translacji z bufora
            \App\Registry::$translate = $cachedTranslate;
            FrontController::getInstance()->getProfiler()->event(self::KERNEL_PROFILER_PREFIX . ': load translate cache');
            return $this;
        }
        //utworzenie obiektu tłumaczenia
        \App\Registry::$translate = new \Mmi\Translate;
        //dodawanie tłumaczeń do translatora
        foreach ($structure as $languageData) {
            foreach ($languageData as $lang => $translationData) {
                \App\Registry::$translate->addTranslation(is_array($translationData) ? $translationData[0] : $translationData, $lang);
            }
        }
        //zapis do cache
        if ($cache !== null) {
            $cache->save(\App\Registry::$translate, $key, 0);
        }
        //event profilera
        FrontController::getInstance()->getProfiler()->event(self::KERNEL_PROFILER_PREFIX . ': translations added');
        return $this;
    }

    /**
     * Inicjalizacja sesji
     * @return \Mmi\App\Bootstrap
     */
    protected function _setupSession()
    {
        //brak sesji
        if (!\App\Registry::$config->session || !\App\Registry::$config->session->name) {
            return $this;
        }
        //własna sesja, oparta na obiekcie implementującym SessionHandlerInterface
        if (strtolower(\App\Registry::$config->session->handler) == 'user') {
            //nazwa klasy sesji
            $sessionClass = \App\Registry::$config->session->path;
            //ustawienie handlera
            session_set_save_handler(new $sessionClass);
        }
        try {
            //uruchomienie sesji
            \Mmi\Session\Session::start(\App\Registry::$config->session);
        } catch (\Mmi\App\KernelException $e) {
            //błąd uruchamiania sesji
            FrontController::getInstance()->getLogger()->error('Unable to start session');
        }
        return $this;
    }

    /**
     * Inicjalizacja bufora FrontControllera
     * @return \Mmi\App\Bootstrap
     */
    protected function _setupLocalCache()
    {
        //brak konfiguracji cache
        if (!\App\Registry::$config->localCache) {
            \App\Registry::$config->localCache = new \Mmi\Cache\CacheConfig;
            \App\Registry::$config->localCache->active = 0;
        }
        //ustawienie bufora systemowy aplikacji
        FrontController::getInstance()->setLocalCache(new \Mmi\Cache\Cache(\App\Registry::$config->localCache));
        //wstrzyknięcie cache do ORM
        \Mmi\Orm\DbConnector::setCache(FrontController::getInstance()->getLocalCache());
        return $this;
    }

    /**
     * Inicjalizacja bufora
     * @return \Mmi\App\Bootstrap
     */
    protected function _setupCache()
    {
        //brak konfiguracji cache
        if (!\App\Registry::$config->cache) {
            return $this;
        }
        //cache użytkownika
        \App\Registry::$cache = new \Mmi\Cache\Cache(\App\Registry::$config->cache);
        return $this;
    }

    /**
     * Ustawianie przechowywania
     * @return \Mmi\App\Bootstrap
     */
    protected function _setupDatabase()
    {
        //brak konfiguracji bazy
        if (!\App\Registry::$config->db || !\App\Registry::$config->db->driver) {
            return $this;
        }
        //obliczanie nazwy drivera
        $driver = '\\Mmi\\Db\\Adapter\\Pdo' . ucfirst(\App\Registry::$config->db->driver);
        //próba powołania drivera
        \App\Registry::$db = new $driver(\App\Registry::$config->db);
        //wstrzyknięcie profilera do adaptera bazodanowego
        \App\Registry::$db->setProfiler(new \Mmi\Db\DbProfiler);
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
    protected function _setupFrontController(\Mmi\Mvc\Router $router, \Mmi\Mvc\View $view)
    {
        //inicjalizacja frontu
        $frontController = FrontController::getInstance();
        //wczytywanie struktury frontu z cache
        if (null === ($frontStructure = FrontController::getInstance()->getLocalCache()->load($cacheKey = 'mmi-structure'))) {
            FrontController::getInstance()->getLocalCache()->save($frontStructure = \Mmi\Mvc\Structure::getStructure(), $cacheKey, 0);
        }
        //konfiguracja frontu
        FrontController::getInstance()->setStructure($frontStructure)
            //ustawienie routera
            ->setRouter($router)
            //ustawienie widoku
            ->setView($view)
            //włączenie (lub nie) debugera
            ->getResponse()->setDebug(\App\Registry::$config->debug);
        //rejestracja pluginów
        foreach (\App\Registry::$config->plugins as $plugin) {
            $frontController->registerPlugin(new $plugin());
        }
        return $this;
    }

    /**
     * Inicjalizacja widoku
     * @param \Mmi\Mvc\Router $router
     * @return \Mmi\Mvc\View
     */
    protected function _setupView(\Mmi\Mvc\Router $router)
    {
        //powołanie i konfiguracja widoku
        return (new \Mmi\Mvc\View)->setCache(FrontController::getInstance()->getLocalCache())
            //opcja kompilacji
            ->setAlwaysCompile(\App\Registry::$config->compile)
            //ustawienie cdn
            ->setCdn(\App\Registry::$config->cdn)
            //ustawienie requestu
            ->setRequest(FrontController::getInstance()->getRequest())
            //ustawianie baseUrl
            ->setBaseUrl(FrontController::getInstance()->getEnvironment()->baseUrl);
    }
}
