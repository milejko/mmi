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
use Mmi\Http\Request;
use Mmi\Http\Response;
use Mmi\Mvc\ActionHelper;
use Symfony\Component\Dotenv\Dotenv;

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
    protected $container;

    /**
     * @var AppProfiler
     */
    protected $profiler;

    /**
     * Constructor
     */
    public function __construct()
    {
        //enable profiler
        $this->profiler = new AppProfiler();
        //configure application
        $this->configureEnvironment()
            ->buildContainer();
    }

    /**
     * Application run
     */
    public function run(): void
    {
        //set error handler
        $this->setErrorHandler();
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

    /**
     * Builds container
     */
    private function buildContainer(): self
    {
        //remove previous compilation if cache disabled
        \getenv('CACHE_PRIVATE_ENABLED') || $this->unlinkCompiledContainer();
        //try to build from cache
        $this->container = $this->getContainerBuilder()->build();
        //container is not empty ()
        if ($this->container->has('app.structure.template')) {
            $this->profiler->event(self::PROFILER_PREFIX . 'cached DI container loaded');
            return $this;
        }
        //unlink previously cached container
        $this->unlinkCompiledContainer();
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
        //controllers, add view helpers, commands
        foreach ($structure['classes'] as $classAlias => $classFqn) {
            $builder->addDefinitions([$classAlias => autowire($classFqn)]);
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
        $builder = (new ContainerBuilder())
            ->useAutowiring(true)
            ->useAnnotations(true)
            ->ignorePhpDocErrors(true)
            ->enableCompilation(self::APPLICATION_COMPILE_PATH)
            ->writeProxiesToFile(true, self::APPLICATION_COMPILE_PATH)
            ->addDefinitions([AppProfilerInterface::class => $this->profiler]);
        return $this->isApcuEnabled() ?
            $builder->enableDefinitionCache(\BASE_PATH) :
            $builder;

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
        //.env loading
        (new Dotenv())->usePutenv()->load(BASE_PATH . '/.env');
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

    /**
     * Unlinks compiled container
     */
    private function unlinkCompiledContainer(): void
    {
        $this->isApcuEnabled() && \apcu_clear_cache();
        array_map('unlink', glob(self::APPLICATION_COMPILE_PATH . '/CompiledContainer.php'));
    }

    /**
     * APCu enabled
     */
    private function isApcuEnabled(): bool
    {
        return function_exists('apcu_fetch')
            && ini_get('apc.enabled')
            && ! ('cli' === \PHP_SAPI && !ini_get('apc.enable_cli'));
    }

}
