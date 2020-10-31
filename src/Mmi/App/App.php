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
use DI\NotFoundException;
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

    private function configureContainer(): self
    {
        //remove previous compilation if cache disabled
        if (!$_ENV['CACHE_PRIVATE_ENABLED']) {
            array_map('unlink', glob(self::APPLICATION_COMPILE_PATH . '/*'));
        }
        try {
            //try to build from cache
            $container = $this->getContainerBuilder()->build();
            //below throws exception if container cache is empty
            $container->get('app.structure');
            self::$di = $container;
            $this->profiler->event(self::PROFILER_PREFIX . 'cached DI container loaded');
            return $this;
        } catch (NotFoundException $e) {
        }
        //create container builder
        $builder = $this->getContainerBuilder();
        $builder->addDefinitions(['app.structure' => $structure = Structure::getStructure()]);
        //add module DI definitions
        foreach ($structure['di'] as $diConfigPath) {
            $builder->addDefinitions($diConfigPath);
        }
        //add controllers
        foreach ($structure['module'] as $moduleName => $controllers) {
            $definitions = [];
            foreach ($controllers as $controller => $actions) {
                $controllerClassName = \ucfirst($moduleName) . '\\' . \ucfirst($controller) . 'Controller';
                $definitions[$controllerClassName] = autowire($controllerClassName);
            }
            $builder->addDefinitions($definitions);
        }
        //build container
        self::$di = $builder->build();
        $this->profiler->event(self::PROFILER_PREFIX . 'DI container built');        
        return $this;
    }

    private function getContainerBuilder(): ContainerBuilder
    {
        $this->profiler->event(self::PROFILER_PREFIX . 'build DI container');
        //create container builder
        $builder = new ContainerBuilder();
        //configure builder
        $builder
            ->useAutowiring(true)
            ->useAnnotations(true)
            ->ignorePhpDocErrors(true)
            ->addDefinitions([AppProfilerInterface::class => $this->profiler]);
            $builder->enableCompilation(self::APPLICATION_COMPILE_PATH)
                ->writeProxiesToFile(true, self::APPLICATION_COMPILE_PATH);
        return $builder;
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
        //ENV is invalid
        if (!isset($_ENV['CACHE_PRIVATE_ENABLED'])) {
            throw new KernelException('CACHE_PRIVATE_ENABLED is not specified in the environment');
        }        
        $this->profiler->event(self::PROFILER_PREFIX . 'environment configured');
        return $this;
    }

}
