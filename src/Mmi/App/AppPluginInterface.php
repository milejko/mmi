<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

use Mmi\Http\Request;

interface AppPluginInterface
{

    /**
     * Executed right after plugin is registered
     */
    public function afterRegistered(): void;

    /**
     * Executed before dispatching
     */
    public function beforeDispatch(Request $request): void;

    /**
     * Executed after dispatching
     */
    public function afterDispatch(Request $request): void;

    /**
     * Executed before sending content
     */
    public function beforeSend(Request $request): void;

}
