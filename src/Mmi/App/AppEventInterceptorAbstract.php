<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Psr\Container\ContainerInterface;

/**
 * Application event interceptor abstract class
 */
abstract class AppEventInterceptorAbstract
{
    /**
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Inject complete container
     */
    public final function __construct(ContainerInterface $container) {
        $this->container = $container;
        $this->init();
    }

    /**
     * Executed right after interceptor is added
     */
    abstract public function init(): void;

    /**
     * Executed before dispatching
     */
    abstract public function beforeDispatch(): void;

    /**
     * Executed after dispatching
     */
    abstract public function afterDispatch(): void;

    /**
     * Executed before sending content
     */
    abstract public function beforeSend(): void;

}
