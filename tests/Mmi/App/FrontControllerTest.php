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
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance(true));
        ob_end_clean();
    }

    public function testRegisterPlugin()
    {
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->registerPlugin(new \Mmi\App\FrontControllerPluginAbstract));
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->registerPlugin(new \Mmi\App\FrontControllerPluginAbstract));
        $this->assertCount(2, FrontController::getInstance()->getPlugins());
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testGetEmptyStructure()
    {
        FrontController::getInstance()->getStructure();
    }

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testSetStructure()
    {
        $sampleArray = \Mmi\Mvc\Structure::getStructure();
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setStructure($sampleArray));
        $this->assertSame($sampleArray, FrontController::getInstance()->getStructure());
        $this->assertArrayHasKey('mmi', FrontController::getInstance()->getStructure('template'));
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
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setRouter($sampleRouter));
        $this->assertSame($sampleRouter, FrontController::getInstance()->getRouter());
    }

    public function testSetRequest()
    {
        $sampleRequest = new \Mmi\Http\Request(['module' => 'test']);
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setRequest($sampleRequest));
        $this->assertSame($sampleRequest, FrontController::getInstance()->getRequest());
    }

    public function testSetProfiler()
    {
        $sampleProfiler = new \Mmi\App\NullKernelProfiler;
        $this->assertInstanceOf('\Mmi\App\NullKernelProfiler', FrontController::getInstance()->getProfiler());
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setProfiler($sampleProfiler));
        $this->assertSame($sampleProfiler, FrontController::getInstance()->getProfiler());
    }

    public function testSetResponse()
    {
        $sampleResponse = (new \Mmi\Http\Response())->setContent('test content');
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setResponse($sampleResponse));
        $this->assertSame($sampleResponse, FrontController::getInstance()->getResponse());
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
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setLocalCache($sampleCache));
        $this->assertSame($sampleCache, FrontController::getInstance()->getLocalCache());
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
        $this->assertInstanceOf(self::CLASS_NAME, FrontController::getInstance()->setView($sampleView));
        $this->assertSame($sampleView, FrontController::getInstance()->getView());
    }

    public function testGetEnvironment()
    {
        $this->assertInstanceOf('\Mmi\Http\HttpServerEnv', FrontController::getInstance()->getEnvironment());
    }

    public function testGetLogger()
    {
        $this->assertInstanceOf('\Psr\Log\LoggerInterface', FrontController::getInstance()->getLogger());
    }

    /**
     * @expectedException \Mmi\Mvc\MvcNotFoundException
     */
    public function testRun()
    {
        ob_start();
        //założenie bufora odpowiedzi
        FrontController::getInstance()->run();
        //sprawdzenie renderingu akcji
        $this->assertEquals(\Mmi\IndexController::DEFAULT_LABEL, ob_get_contents());
        ob_end_clean();
        //tu 404
        FrontController::getInstance()
            //podłożenie błednego routera
            ->setRouter(new \Mmi\Mvc\Router((new \Mmi\Mvc\RouterConfig())->setRoute('test', '', ['module' => 'a', 'controller' => 'b', 'action' => 'c'])))
            ->run();
    }

}
