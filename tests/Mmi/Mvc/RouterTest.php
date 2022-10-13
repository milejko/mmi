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

    public function testEncodeDecodeUrl()
    {
        $sampleRoute = new \Mmi\Mvc\RouterConfigRoute;
        $sampleRoute->name = 'test';
        $sampleRoute->pattern = '/([0-9]+)-([0-9]+)/i';
        $sampleRoute->replace = ['module' => '$1', 'controller' => '$2', 'action' => 'test'];
        $config = (new RouterConfig)
            ->setRoutes([$sampleRoute])
            ->setRoute('test1', '/([a-z]+)-([a-z]+)-([a-z]+)/', ['module' => '$1', 'controller' => '$2', 'action' => '$3'])
            ->setRoute('test2', 'static', ['module' => 'static'], ['controller' => 'index', 'action' => 'index'])
            ->setRoute('test3', '/numeric\/([0-9])+/', ['module' => 'test', 'val' => '$1'], ['controller' => 'index', 'action' => 'index']);

        $router = new Router($config);
        $this->assertSame($config, $router->getConfig());
        $this->assertEquals(['module' => 'static', 'controller' => 'index', 'action' => 'index'], $router->decodeUrl('static'));
        $this->assertEquals(['module' => 'mod', 'controller' => 'con', 'action' => 'act'], $router->decodeUrl('mod-con-act'));
        $this->assertEquals(['module' => '2', 'controller' => '2', 'action' => 'test'], $router->decodeUrl('2-2'));
        $this->assertEquals(['module' => '2', 'controller' => '2', 'action' => 'test', 'id' => 2], $router->decodeUrl('2-2?id=2'));
        $this->assertEquals(['module' => 'test', 'controller' => 'index', 'action' => 'index'], $router->decodeUrl('?module=test'));
        $this->assertEquals(['module' => 'test', 'controller' => 'index', 'action' => 'index', 'val' => 2], $router->decodeUrl('numeric/12?action=index'));

        $this->assertEquals('/test-index-index', $router->encodeUrl(['module' => 'test', 'controller' => 'index', 'action' => 'index']));
        $this->assertEquals('/test-index-index/?id=2', $router->encodeUrl(['module' => 'test', 'controller' => 'index', 'action' => 'index', 'id' => 2]));
        $this->assertEquals(['module' => 'test', 'controller' => 'index', 'action' => 'index', 'id' => 2], $router->decodeUrl('/test-index-index/?id=2'));
        $this->assertEquals('', $router->encodeUrl([]));

        //czyszczenie routera
        $router->getConfig()->setRoutes([], true);
        $this->assertEquals('/?module=mmi', $router->encodeUrl(['module' => 'mmi', 'controller' => 'index', 'action' => 'index']));
    }

    public function testInvalidRouter()
    {
        $this->expectException(\Mmi\Mvc\MvcException::class);
        $config = (new RouterConfig)
            ->setRoute('test', '/static/', ['module' => '$1']);
        $router = new Router($config);
        $router->decodeUrl('static');
    }

}
