<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Router;

use Mmi\Mvc\Router,
    Mmi\Mvc\RouterConfig;

class RouterTest extends \PHPUnit\Framework\TestCase
{

    public function testGetContentType()
    {
        $config = (new RouterConfig)
            ->setRoute('test1', '/([a-z]+)-([a-z]+)-([a-z]+)/i', ['module' => '$1', 'controller' => '$2', 'action' => '$3'])
            ->setRoute('test2', 'static', ['module' => 'static'], ['controller' => 'index', 'action' => 'index']);
        $router = new Router($config);
        $this->assertEquals(['module' => 'static', 'controller' => 'index', 'action' => 'index'], $router->decodeUrl('static'));
        $this->assertEquals(['module' => 'mod', 'controller' => 'con', 'action' => 'act'], $router->decodeUrl('mod-con-act'));
    }

}
