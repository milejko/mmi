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
     * @var AppProfiler
     */
    private $profiler;

    /**
     * Constructor
     */
    public function __construct()
    {
        //enable profiler
        $this->profiler = new AppProfiler();
        //configure application
        $this
            ->configureEnvironment()
            ->configureStructure()
            ->configureContainer()
            ->configureErrorHandler();
    }

    /**
     * Uruchomienie aplikacji
     */
    public function run(): void
    {
        $profiler = self::$di->get(AppProfilerInterface::class);
        $request = self::$di->get(Request::class);
        //plugins before dispatch
        foreach (self::$plugins as $plugin) {
            $plugin->beforeDispatch($request);
            $profiler->event(self::PROFILER_PREFIX . ': ' . \get_class($plugin) . ' executed beforeDispatch');
        }
        //render content
        $content = self::$di->get(ActionHelper::class)->forward($request);
        //plugins before dispatch
        foreach (self::$plugins as $plugin) {
            $plugin->beforeSend($request);
            $profiler->event(self::PROFILER_PREFIX . ': ' . \get_class($plugin) . ' executed beforeSend');
        }
        $profiler->event(self::PROFILER_PREFIX . 'start sending content');
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
    private function getStructure(bool $cacheEnabled = false): array
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

    private function configureStructure(): self
    {
        $this->profiler->event(self::PROFILER_PREFIX . 'map application');
        //always parse structure in a non production mode
        if (!$_ENV['CACHE_PRIVATE_ENABLED']) {
            //overwrite cache
            \file_put_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE, \json_encode($structure = Structure::getStructure()));
            self::$structure = $structure;
            $this->profiler->event(self::PROFILER_PREFIX . 'application mapped');
            return $this;
        }
        //try fetch structure from a compiled json
        if (null !== $structure = @\json_decode(\file_get_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE), true)) {
            self::$structure = $structure;
            $this->profiler->event(self::PROFILER_PREFIX . 'loaded cached application map');
            return $this;
        }
        //cache compiled json
        \file_put_contents(self::APPLICATION_COMPILE_STRUCTURE_FILE, \json_encode($structure = Structure::getStructure()));
        self::$structure = $structure;
        $this->profiler->event(self::PROFILER_PREFIX . 'application mapped and cached');
        return $this;
    }

    private function configureContainer(): self
    {
        $this->profiler->event(self::PROFILER_PREFIX . 'build DI container');
        //create container builder
        $builder = new ContainerBuilder();
        $builder->useAutowiring(true)
            ->useAnnotations(true)
            ->ignorePhpDocErrors(true);
        if (!isset($_ENV['CACHE_PRIVATE_ENABLED'])) {
            throw new KernelException('CACHE_PRIVATE_ENABLED is not specified in the environment');
        }
        //private cache enabled
        if ($_ENV['CACHE_PRIVATE_ENABLED']) {
            $builder->enableCompilation(self::APPLICATION_COMPILE_PATH)
                ->writeProxiesToFile(true, self::APPLICATION_COMPILE_PATH);
        } else {
            array_map('unlink', glob(self::APPLICATION_COMPILE_PATH . '/*'));
        }
        //add module DI definitions
        foreach (self::$structure['di'] as $diConfigPath) {
            $builder->addDefinitions($diConfigPath);
        }
        //add controllers
        foreach (self::$structure['module'] as $moduleName => $controllers) {
            $definitions = [];
            foreach ($controllers as $controller => $actions) {
                $controllerClassName = \ucfirst($moduleName) . '\\' . \ucfirst($controller) . 'Controller';
                $definitions[$controllerClassName] = autowire($controllerClassName);
            }
            $builder->addDefinitions($definitions);
        }
        //build container
        self::$di = $builder->build();
        //add previously created profiler
        self::$di->set(AppProfilerInterface::class, $this->profiler);
        $this->profiler->event(self::PROFILER_PREFIX . 'DI container built');        
        return $this;
    }

    private function configureErrorHandler(): self
    {
        //exception handler
        set_exception_handler([self::$di->get(AppEventHandler::class), 'exceptionHandler']);
        //error handler
        set_error_handler([self::$di->get(AppEventHandler::class), 'errorHandler']);
        return $this;        
    }

    private function configureEnvironment(): self
    {
        $this->profiler->event(self::PROFILER_PREFIX . 'configure environment');
        //encoding settings
        mb_internal_encoding('utf-8');
        ini_set('default_charset', 'utf-8');
        setlocale(LC_ALL, 'pl_PL.utf-8');
        setlocale(LC_NUMERIC, 'en_US.UTF-8');
        //.env loading (unsafe as PHP-DI uses getenv internally)
        Dotenv::createUnsafeImmutable(BASE_PATH)->safeLoad();
        $this->profiler->event(self::PROFILER_PREFIX . 'environment configured');
        return $this;
    }

}
