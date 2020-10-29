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
     * @var Config
     */
    protected $_config;

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
            ->_setupFrontController($this->_setupRouter(), $this->_setupView())
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
        return new \Mmi\Mvc\Router($this->_config->router ? $this->_config->router : new \Mmi\Mvc\RouterConfig);
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
        if (!in_array(FrontController::getInstance()->getEnvironment()->lang, $this->_config->languages)) {
            return $this;
        }
        //ustawianie locale ze środowiska
        $this->_translate->setLocale(FrontController::getInstance()->getEnvironment()->lang);
        return $this;
    }

    /**
     * Ustawia tłumaczenia
     * @return \Mmi\App\Bootstrap
     */
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
            $this->_translate = $cachedTranslate;
            FrontController::getInstance()->getProfiler()->event(self::KERNEL_PROFILER_PREFIX . ': load translate cache');
            return $this;
        }
        //utworzenie obiektu tłumaczenia
        $this->_translate = new \Mmi\Translate;
        //dodawanie tłumaczeń do translatora
        foreach ($structure as $languageData) {
            foreach ($languageData as $lang => $translationData) {
                $this->_translate->addTranslation(is_array($translationData) ? $translationData[0] : $translationData, $lang);
            }
        }
        //zapis do cache
        if ($cache !== null) {
            $cache->save($this->_translate, $key, 0);
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
        if (!$this->_config->session || !$this->_config->session->name) {
            return $this;
        }
        //własna sesja, oparta na obiekcie implementującym SessionHandlerInterface
        if (strtolower($this->_config->session->handler) == 'user') {
            //nazwa klasy sesji
            $sessionClass = $this->_config->session->path;
            //ustawienie handlera
            session_set_save_handler(new $sessionClass);
        }
        try {
            //uruchomienie sesji
            \Mmi\Session\Session::start($this->_config->session);
        } catch (\Mmi\App\KernelException $e) {
            //błąd uruchamiania sesji
            FrontController::getInstance()->getLogger()->error('Unable to start session, reason: ' . $e->getMessage());
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
        if (!$this->_config->localCache) {
            $this->_config->localCache = new \Mmi\Cache\CacheConfig;
            $this->_config->localCache->active = 0;
        }
        //ustawienie bufora systemowy aplikacji
        //FrontController::getInstance()->setLocalCache(new \Mmi\Cache\Cache($this->_config->localCache));
        //wstrzyknięcie cache do ORM
        //\Mmi\Orm\DbConnector::setCache(FrontController::getInstance()->getLocalCache());
        return $this;
    }

    /**
     * Inicjalizacja bufora
     * @return \Mmi\App\Bootstrap
     */
    protected function _setupCache()
    {
        //brak konfiguracji cache
        if (!$this->_config->cache) {
            return $this;
        }
        //cache użytkownika
        $this->_cache = new \Mmi\Cache\Cache($this->_config->cache);
        return $this;
    }

    /**
     * Ustawianie przechowywania
     * @return \Mmi\App\Bootstrap
     */
    protected function _setupDatabase()
    {
        //brak konfiguracji bazy
        if (!$this->_config->db || !$this->_config->db->driver) {
            return $this;
        }
        //obliczanie nazwy drivera
        $driver = '\\Mmi\\Db\\Adapter\\Pdo' . ucfirst($this->_config->db->driver);
        //próba powołania drivera
        $this->_db = new $driver($this->_config->db);
        //wstrzyknięcie profilera do adaptera bazodanowego
        $this->_db->setProfiler(new \Mmi\Db\DbProfiler);
        //wstrzyknięcie do ORM
        \Mmi\Orm\DbConnector::setAdapter($this->_db);
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
        //konfiguracja frontu
        FrontController::getInstance()
            //ustawienie routera
            ->setRouter($router)
            //ustawienie widoku
            ->setView($view)
            //włączenie (lub nie) debugera
            ->getResponse()->setDebug($this->_config->debug);
        //rejestracja pluginów
        foreach ($this->_config->plugins as $plugin) {
            //FrontController::getInstance()->registerPlugin(new $plugin());
        }
        return $this;
    }

    /**
     * Inicjalizacja widoku
     * @return \Mmi\Mvc\View
     */
    protected function _setupView()
    {
        //powołanie i konfiguracja widoku
        return (new \Mmi\Mvc\View)->setCache(FrontController::getInstance()->getLocalCache())
            //opcja kompilacji
            ->setAlwaysCompile($this->_config->compile)
            //ustawienie cdn
            ->setCdn($this->_config->cdn)
            //ustawienie requestu
            ->setRequest(FrontController::getInstance()->getRequest())
            //ustawianie baseUrl
            ->setBaseUrl(FrontController::getInstance()->getEnvironment()->baseUrl);
    }
}
