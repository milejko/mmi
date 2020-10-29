<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use DI\ContainerBuilder;
use DI\Container;
use Mmi\Mvc\Structure;
use Dotenv\Dotenv;
use Mmi\Http\Request;
use Mmi\Http\Response;
use Mmi\Mvc\ActionHelper;

use function DI\autowire;

/**
 * Application class
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
     * @var array
     */
    public static $structure;

    /**
     * @var AppPluginInterface[]
     */
    private static $plugins = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        //enable profiler
        $profiler = new AppProfiler();
        //start aplikacji
        $profiler->event(self::PROFILER_PREFIX . 'application startup');
        //encoding settings
        mb_internal_encoding('utf-8');
        ini_set('default_charset', 'utf-8');
        setlocale(LC_ALL, 'pl_PL.utf-8');
        setlocale(LC_NUMERIC, 'en_US.UTF-8');
        //.env loading (unsafe as PHP-DI uses getenv internally)
        $dotenv = Dotenv::createUnsafeImmutable(BASE_PATH);
        $dotenv->load();
        $profiler->event(self::PROFILER_PREFIX . '.env loaded');
        //konfiguracja kontenera
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true)
            ->useAnnotations(true)
            ->ignorePhpDocErrors(true);
        if (!isset($_ENV['CACHE_PRIVATE_ENABLED'])) {
            throw new KernelException('CACHE_PRIVATE_ENABLED is not specified in the environment');
        }
        //private cache enabled
        if ($cacheEnabled = $_ENV['CACHE_PRIVATE_ENABLED']) {
            $builder->enableCompilation(self::APPLICATION_COMPILE_PATH)
                ->writeProxiesToFile(true, self::APPLICATION_COMPILE_PATH);
        }
        self::$structure = $this->_getStructure($cacheEnabled);
        //add container definitions
        foreach (self::$structure['di'] as $diConfigPath) {
            $builder->addDefinitions($diConfigPath);
        }
        foreach (self::$structure['module'] as $moduleName => $controllers) {
            $definitions = [];
            foreach ($controllers as $controller => $actions) {
                $controllerClassName = \ucfirst($moduleName) . '\\' . \ucfirst($controller) . 'Controller';
                $definitions[$controllerClassName] = autowire($controllerClassName);
            }
            $builder->addDefinitions($definitions);
        }
        $profiler->event(self::PROFILER_PREFIX . 'DI definitions added');
        //build container
        self::$di = $builder->build();
        //add previously created profiler
        self::$di->set(AppProfilerInterface::class, $profiler);
        //exception handler
        set_exception_handler([self::$di->get(AppEventHandler::class), 'exceptionHandler']);
        //error handler
        set_error_handler([self::$di->get(AppEventHandler::class), 'errorHandler']);
        $profiler->event(self::PROFILER_PREFIX . 'DI container built');
    }

    /**
     * Uruchomienie aplikacji
     */
    public function run(string $bootstrapClassName = null): void
    {
        $request = self::$di->get(Request::class);
        //plugins before dispatch
        foreach (self::$plugins as $plugin) {
            $plugin->beforeDispatch($request);
            self::$di->get(AppProfilerInterface::class)->event(self::PROFILER_PREFIX . ': ' . \get_class($plugin) . ' executed beforeDispatch');
        }
        //render content
        $content = self::$di->get(ActionHelper::class)->forward($request);
        //plugins before dispatch
        foreach (self::$plugins as $plugin) {
            $plugin->beforeSend($request);
            self::$di->get(AppProfilerInterface::class)->event(self::PROFILER_PREFIX . ': ' . \get_class($plugin) . ' executed beforeSend');
        }
        //send content to user
        self::$di->get(Response::class)
            ->setContent($content)
            ->send();
    }

    /**
     * Registers plugins
     */
    public static function registerPlugin(AppPluginInterface $plugin): void
    {
        self::$plugins[] = $plugin;
        $plugin->afterRegistered();
    }

    /**
     * Gets plugins
     * @var AppPluginInterface[]
     */
    public static function getPlugins(): array
    {
        return self::$plugins;
    }

    /**
     * Gets the application structure
     */
    private function _getStructure(bool $cacheEnabled = false): array
    {
        //always parse structure in a non production mode
        if (!$cacheEnabled) {
            //overwrite cache
            \file_put_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE, \json_encode($structure = Structure::getStructure()));
            return Structure::getStructure();
        }
        //try fetch structure from a compiled json
        if (null !== $structure = @\json_decode(\file_get_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE), true)) {
            return $structure;
        }
        //cache compiled json
        \file_put_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE, \json_encode($structure = Structure::getStructure()));
        return $structure;
    }

}
