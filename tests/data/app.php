<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

/**
 * Application class
 * @deprecated
 */
class TestApp extends AppAbstract
{
    const PROFILER_PREFIX                    = 'Mmi\TestApp: ';
    const APPLICATION_COMPILE_PATH           = BASE_PATH . '/var/compile';

    /**
     * Sets error and exception handler
     */
    protected function setErrorHandler(): self
    {
        //exception handler
        set_exception_handler([$this->container->get(AppErrorHandler::class), 'exceptionHandler']);
        //error handler
        set_error_handler([$this->container->get(AppErrorHandler::class), 'errorHandler']);
        $this->profiler->event(self::PROFILER_PREFIX . 'error handler setup');
        return $this;
    }

    /**
     * Application run
     */
    public function run(): void
    {
        $interceptor = $this->container->has(AppEventInterceptorInterface::class) ? $this->container->get(AppEventInterceptorInterface::class) : null;
        //intercept before dispatch
        if (null !== $interceptor) {
            $interceptor->init();
            $this->profiler->event(self::PROFILER_PREFIX . 'interceptor init()');
            $interceptor->beforeDispatch();
            $this->profiler->event(self::PROFILER_PREFIX . 'interceptor beforeDispatch()');
        }
    }

}