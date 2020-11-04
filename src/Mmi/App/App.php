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
    const PROFILER_PREFIX                    = 'Mmi\App: ';
    const APPLICATION_COMPILE_PATH           = BASE_PATH . '/var/compile';
    const APPLICATION_COMPILE_STRUCTURE_FILE = self::APPLICATION_COMPILE_PATH . '/Structure.json';

    /**
     * @TODO: remove after all legacy dependencies are removed
     * @var Container
     */
    public static $di;

    /**
     * @var Container
     */
    private $container;

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
        $this->configureEnvironment()
            ->buildContainer()
            ->setErrorHandler();
    }

    /**
     * Uruchomienie aplikacji
     */
    public function run(): void
    {
        //@TODO: remove after target refactoring
        self::$di = $this->container;
        $profiler = $this->container->get(AppProfilerInterface::class);
        $request = $this->container->get(Request::class);            
        $interceptor = $this->container->has(AppEventInterceptorAbstract::class) ? $this->container->get(AppEventInterceptorAbstract::class) : null;
        //intercept before dispatch
        if (null !== $interceptor) {
            $interceptor->beforeDispatch();
            $profiler->event(self::PROFILER_PREFIX . 'interceptor executed beforeDispatch');
        }
        //render content
        $content = $this->container->get(ActionHelper::class)->forward($request);
        //intercept before send
        if (null !== $interceptor) {
            $interceptor->beforeSend();
            $profiler->event(self::PROFILER_PREFIX . 'interceptor executed beforeSend');
        }
        //set content to response
        $this->container->get(Response::class)
            ->setContent($content);
        //content send
        $profiler->event(self::PROFILER_PREFIX . 'send to client');
        $this->container->get(Response::class)->send();
    }

    private function buildContainer(): self
    {
        //remove previous compilation if cache disabled
        if (!getenv('CACHE_PRIVATE_ENABLED')) {
            array_map('unlink', glob(self::APPLICATION_COMPILE_PATH . '/CompiledContainer*'));
        }
        //try to build from cache
        $this->container = $this->getContainerBuilder()->build();
        //container is not empty ()
        if ($this->container->has('app.structure.template')) {
            $this->profiler->event(self::PROFILER_PREFIX . 'cached DI container loaded');
            return $this;
        }
        //unlink previously cached container
        array_map('unlink', glob(self::APPLICATION_COMPILE_PATH . '/CompiledContainer*'));
        //create container builder
        $builder = $this->getContainerBuilder();
        //get structure
        $structure = Structure::getStructure();
        //add structure to the container
        $builder->addDefinitions(['app.structure.template' => $structure['template']]);
        $this->profiler->event(self::PROFILER_PREFIX . 'application structure mapped');
        //add module DI definitions
        foreach ($structure['di'] as $diConfigPath) {
            $builder->addDefinitions($diConfigPath);
        }
        //add controllers
        foreach ($structure['module'] as $moduleName => $controllers) {
            foreach ($controllers as $controller => $count) {
                $controllerClassName = \ucfirst($moduleName) . '\\' . \ucfirst($controller) . 'Controller';
                $builder->addDefinitions([$controllerClassName => autowire($controllerClassName)]);
            }
        }
        //add view helpers
        foreach ($structure['helper'] as $moduleName => $helpers) {
            foreach ($helpers as $helperName => $count) {
                $helperClassName = \ucfirst($moduleName) . '\\Mvc\\ViewHelper\\' . \ucfirst($helperName);
                $builder->addDefinitions(['helper\\' . $helperName => autowire($helperClassName)]);
            }
        }
        //add filters
        foreach ($structure['filter'] as $moduleName => $filters) {
            foreach ($filters as $filterName => $count) {
                $filterClassName = \ucfirst($moduleName) . '\\Filter\\' . \ucfirst($filterName);
                $builder->addDefinitions(['filter\\' . $filterName => autowire($filterClassName)]);
            }
        }
        //build container
        $this->container = $builder->build();
        $this->profiler->event(self::PROFILER_PREFIX . 'DI container built');        
        return $this;
    }

    /**
     * Gets configured container builder
     */
    private function getContainerBuilder(): ContainerBuilder
    {
        //configure and return builder
        return (new ContainerBuilder())->useAutowiring(true)
            ->useAnnotations(true)
            ->ignorePhpDocErrors(true)
            ->enableCompilation(self::APPLICATION_COMPILE_PATH)
            ->writeProxiesToFile(true, self::APPLICATION_COMPILE_PATH)
            ->addDefinitions([AppProfilerInterface::class => $this->profiler]);
    }

    /**
     * Configures environment (ie. project encoding), and loads .env
     */
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
        $this->profiler->event(self::PROFILER_PREFIX . 'environment configuration loaded');
        return $this;
    }

    /**
     * Sets error and exception handler
     */
    private function setErrorHandler(): self
    {
        //exception handler
        set_exception_handler([$this->container->get(AppErrorHandler::class), 'exceptionHandler']);
        //error handler
        set_error_handler([$this->container->get(AppErrorHandler::class), 'errorHandler']);
        $this->profiler->event(self::PROFILER_PREFIX . 'error handler setup');
        return $this;        
    }

}
