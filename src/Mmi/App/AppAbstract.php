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
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\Dotenv\Exception\PathException;

use function DI\autowire;

/**
 * Application class
 */
abstract class AppAbstract
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
        $this->profiler->event(self::PROFILER_PREFIX . 'application create');
        //configure application
        $this->configureEnvironment()
            ->buildContainer()
            ->setErrorHandler();
    }

    /**
     * Application run method
     */
    abstract public function run(): void;

    /**
     * Sets error and exception handler
     */
    abstract protected function setErrorHandler(): self;

    /**
     * Builds container
     */
    private function buildContainer(): self
    {
        //remove previous compilation if cache disabled
        \getenv('CACHE_SYSTEM_ENABLED') || $this->unlinkCompiledContainer();
        //try to build from cache (@TODO remove static $di after refactoring)
        $this->container = self::$di = $this->getContainerBuilder()->build();
        //container has app.structure.template, so it is properly built
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
        $this->profiler->event(self::PROFILER_PREFIX . 'application structure mapped');
        //add structure to the container
        $builder->addDefinitions(['app.structure.template' => $structure['template']]);
        //add module DI definitions
        foreach ($structure['di'] as $diConfigPath) {
            $builder->addDefinitions($diConfigPath);
        }
        //controllers, add view helpers, commands
        foreach ($structure['classes'] as $classAlias => $classFqn) {
            $builder->addDefinitions([$classAlias => autowire($classFqn)]);
        }
        //build container (@TODO remove static $di after refactoring)
        $this->container = self::$di = $builder->build();
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
            //adding profiler instance
            ->addDefinitions([AppProfilerInterface::class => $this->profiler]);
        return $this->isApcuEnabled() ?
            $builder->enableDefinitionCache(__DIR__) :
            $builder;
    }

    /**
     * Configures environment (ie. project encoding), and loads .env
     */
    private function configureEnvironment(): self
    {
        //encoding settings
        mb_internal_encoding('utf-8');
        ini_set('default_charset', 'utf-8');
        setlocale(LC_ALL, 'pl_PL.utf-8');
        setlocale(LC_NUMERIC, 'en_US.UTF-8');
        //try to load .env
        try {
            (new Dotenv())->usePutenv()->load(BASE_PATH . '/.env');
            $this->profiler->event(self::PROFILER_PREFIX . '.env configuration file loaded');
        } catch (PathException $e) {
            $this->profiler->event(self::PROFILER_PREFIX . 'no .env configuration file found');
        }
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
        return function_exists('apcu_fetch') && ini_get('apc.enabled')
            && !('cli' === \PHP_SAPI && !ini_get('apc.enable_cli'));
    }
}