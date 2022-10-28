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
 * Application event interceptor interface
 */
interface AppEventInterceptorInterface
{
    /**
     * Executed right after interceptor is added
     */
    public function init(): void;

    /**
     * Executed before dispatching
     */
    public function beforeDispatch(): void;

    /**
     * Executed after dispatching
     */
    public function afterDispatch(): void;

    /**
     * Executed before sending content
     */
    public function beforeSend(): void;
}
