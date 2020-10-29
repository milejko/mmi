<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use App\Config;
use DI\ContainerBuilder;
use DI\Container;
use Mmi\Cache\CacheConfig;
use Mmi\Mvc\Structure;
use Dotenv\Dotenv;

/**
 * Klasa aplikacji
 */
class App
{
    const PROFILER_PREFIX                    = 'App: ';
    const APPLICATION_COMPILE_PATH           = BASE_PATH . '/var/compile';
    const APPLICATION_COMPILE_STRUCTURE_FILE = self::APPLICATION_COMPILE_PATH . '/Structure.json';

    /**
     * @var Container
     */
    public static $di;

    /**
     * Konstruktor
     */
    public function __construct()
    {
        //wewnętrzne kodowanie znaków
        mb_internal_encoding('utf-8');
        //domyślne kodowanie znaków PHP
        ini_set('default_charset', 'utf-8');
        //locale
        setlocale(LC_ALL, 'pl_PL.utf-8');
        setlocale(LC_NUMERIC, 'en_US.UTF-8');
        //.env loading (unsafe as PHP-DI uses getenv internally)
        $dotenv = Dotenv::createUnsafeImmutable(BASE_PATH);
        $dotenv->load();
        //ładownie konfiguracji
        $config = $this->_loadConfig();
        //ustawianie konfiguracji loggera
        //$config->log ? \Mmi\Log\LoggerHelper::setConfig($config->log) : null;
        //włączenie profilera
        FrontController::getInstance()->setProfiler(new KernelProfiler);        
        //start aplikacji
        FrontController::getInstance()->getProfiler()->event(self::PROFILER_PREFIX . 'application startup');

        //konfiguracja kontenera
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true);
        $builder->useAnnotations(true);
        $builder->ignorePhpDocErrors(true);
        //flaga compile wyłącza cache
        if (!$config->compile) {
            $builder->enableCompilation(self::APPLICATION_COMPILE_PATH);
            $builder->writeProxiesToFile(true, self::APPLICATION_COMPILE_PATH);
        }
        //ustawianie struktury aplikacji
        FrontController::getInstance()->setStructure($this->_getStructure($config->compile));
        //dodawanie definicji DI
        foreach (FrontController::getInstance()->getStructure('di') as $diConfigPath) {
            $builder->addDefinitions($diConfigPath);
        }
        self::$di = $builder->build();
        //FrontController::getInstance()->setContainer($builder->build());
        self::$di->get(KernelEventHandler::class);
    }

    /**
     * Uruchomienie aplikacji
     */
    public function run(string $bootstrapClassName = null): void
    {
        //sprawdzenie czy klasa konfiguracji istnieje
        if (!class_exists($bootstrapClassName)) {
            throw new KernelException('Application bootstrap class is missing: ' . $bootstrapClassName);
        }
        //bootstrap start
        FrontController::getInstance()->getProfiler()->event(self::PROFILER_PREFIX . 'bootstrap startup');
        $bootstrap = new $bootstrapClassName();
        //bootstrap nie implementuje właściwego interfeace'u
        if (!($bootstrap instanceof \Mmi\App\BootstrapInterface)) {
            throw new KernelException('Application bootstrap should be implementing \Mmi\App\Bootstrap\Interface');
        }
        //bootstrap start
        FrontController::getInstance()->run();
        FrontController::getInstance()->getProfiler()->event(self::PROFILER_PREFIX . 'front controller run');
        
    }

    /**
     * Ładuje konfigurację aplikacji
     * @throws KernelException
     */
    private function _loadConfig(): Config
    {
        //sprawdzenie czy klasa konfiguracji istnieje
        if (!class_exists(Config::class)) {
            throw new \Mmi\App\KernelException('Application configuration class is missing: ' . Config::class);
        }
        $config = new Config();
        if (!($config instanceof AppConfig)) {
            throw new \Mmi\App\KernelException('Application configuration class is not extending AppConfig class');
        }
        //konfiguracja dla danego środowiska
        return new Config();
    }

    private function _getStructure(bool $compile): array
    {
        if ($compile) {
            return Structure::getStructure();
        }
        //struktura wczytana z json (compile)
        if (null !== $structure = @\json_decode(\file_get_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE), true)) {
            return $structure;
        }
        \file_put_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE, \json_encode($structure = Structure::getStructure()));
        return $structure;
    }

    private function _setupPrivateCache(CacheConfig $config): self
    {
        //ustawienie bufora systemowy aplikacji
        //FrontController::getInstance()->setLocalCache(new \Mmi\Cache\Cache($this->_config->localCache));
        //wstrzyknięcie cache do ORM
        //\Mmi\Orm\DbConnector::setCache(FrontController::getInstance()->getLocalCache());
        //return $this;
    }

}
