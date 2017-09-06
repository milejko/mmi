<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\App;

use Mmi\App\FrontController;

/**
 * Test front controllera
 */
class FrontControllerTest extends \PHPUnit\Framework\TestCase
{

    CONST CLASS_NAME = '\Mmi\App\FrontController';
    
    public function testGetInstance()
    {
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance(true), 'Setter does not return self');
    }

    public function testRegisterPlugin()
    {
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->registerPlugin(new \Mmi\App\FrontControllerPluginAbstract), 'Setter does not return self');
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->registerPlugin(new \Mmi\App\FrontControllerPluginAbstract), 'Setter does not return self');
        $this->assertCount(2, FrontController::getInstance()->getPlugins(), '2 plugins should be registered');
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testGetEmptyStructure()
    {
        FrontController::getInstance(true)->getStructure();
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testSetStructure()
    {
        $sampleArray = \Mmi\Mvc\Structure::getStructure();
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setStructure($sampleArray), 'Setter does not return self');
        $this->assertSame($sampleArray, FrontController::getInstance()->getStructure(), 'Structure was modified');
        $this->assertArrayHasKey('mmi', FrontController::getInstance()->getStructure('template'), 'Structure does not contain mmi module');
        FrontController::getInstance()->getStructure('inexistent');
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testGetRouter()
    {
        FrontController::getInstance()->getRouter();
    }

    public function testSetRouter()
    {
        $sampleRouter = new \Mmi\Mvc\Router((new \Mmi\Mvc\RouterConfig())->setRoute('test', '', ['module' => 'mmi', 'controller' => 'index', 'action' => 'test']));
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setRouter($sampleRouter), 'Setter does not return self');
        $this->assertSame($sampleRouter, FrontController::getInstance()->getRouter(), 'Router invalid');
    }

    public function testSetRequest()
    {
        $sampleRequest = new \Mmi\Http\Request(['module' => 'test']);
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setRequest($sampleRequest), 'Setter does not return self');
        $this->assertSame($sampleRequest, FrontController::getInstance()->getRequest(), 'Request invalid');
    }

    public function testSetProfiler()
    {
        $sampleProfiler = new \Mmi\App\NullKernelProfiler;
        $this->assertInstanceOf('\Mmi\App\NullKernelProfiler', FrontController::getInstance()->getProfiler(), 'Invalid profiler class');
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setProfiler($sampleProfiler), 'Setter does not return self');
        $this->assertSame($sampleProfiler, FrontController::getInstance()->getProfiler(), 'Profiler invalid');
    }

    public function testSetResponse()
    {
        $sampleResponse = (new \Mmi\Http\Response())->setContent('test content');
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setResponse($sampleResponse), 'Setter does not return self');
        $this->assertSame($sampleResponse, FrontController::getInstance()->getResponse(), 'Response invalid');
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testGetLocalCache()
    {
        FrontController::getInstance()->getLocalCache();
    }

    public function testSetLocalCache()
    {
        $sampleCache = new \Mmi\Cache\Cache(new \Mmi\Cache\CacheConfig);
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setLocalCache($sampleCache), 'Setter does not return self');
        $this->assertSame($sampleCache, FrontController::getInstance()->getLocalCache(), 'Local cache invalid');
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testGetView()
    {
        FrontController::getInstance()->getView();
    }

    public function testSetView()
    {
        $sampleView = new \Mmi\Mvc\View();
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setView($sampleView), 'Setter does not return self');
        $this->assertSame($sampleView, FrontController::getInstance()->getView(), 'View invalid');
    }

    public function testGetEnvironment()
    {
        $this->assertInstanceOf('\Mmi\Http\HttpServerEnv', FrontController::getInstance()->getEnvironment(), 'Wrong HttpServerEnv class');
    }

    public function testGetLogger()
    {
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', FrontController::getInstance()->getLogger(), 'Logger interface invalid');
    }

    /**
     * @expectedException \Mmi\Mvc\MvcNotFoundException
     */
    public function testRun()
    {
        //założenie bufora odpowiedzi
        $this->assertInstanceOf('\Mmi\Http\Response', $response = FrontController::getInstance()->run());
        //sprawdzenie renderingu akcji
        $this->assertEquals(\Mmi\IndexController::DEFAULT_LABEL, $response->getContent());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('text/html', $response->getType());
        //tu 404
        FrontController::getInstance()
            //podłożenie błednego routera
            ->setRouter(new \Mmi\Mvc\Router((new \Mmi\Mvc\RouterConfig())->setRoute('test', '', ['module' => 'a', 'controller' => 'b', 'action' => 'c'])))
            ->run();
    }

}
