<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\App;

use Mmi\App\Kernel;

/**
 * Test standardowego bootstrapa
 */
class BootstrapTest extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass()
    {
        require 'data/config-cache.php';
        require 'data/config-nodb.php';
        require 'data/config-session.php';
    }

    public function testBootstrap()
    {
        $_SERVER['APPLICATION_LANGUAGE'] = 'pl';
        (new Kernel('\Mmi\App\Bootstrap', 'DEFAULT'))->run();
        $this->_testResponseAfterRun();
    }

    public function testBootstrapCache()
    {
        $_SERVER['APPLICATION_LANGUAGE'] = 'fr';
        (new Kernel('\Mmi\App\Bootstrap', 'CACHE'))->run();
        //dodanie do profilera
        \App\Registry::$db->query('SELECT 1');
        //sprawdzenie czy dodany kernelProfiler
        $this->assertInstanceOf('\Mmi\App\KernelProfiler', \Mmi\App\FrontController::getInstance()->getProfiler());
        $response = $this->_testResponseAfterRun();
        ob_start();
        $this->assertInstanceOf('\Mmi\Http\Response', $response->send(false));
        $html = ob_get_contents();
        $this->assertRegExp('/' . \Mmi\IndexController::DEFAULT_LABEL . '/', $html);
        $this->assertRegExp('/#MmiPanel/', $html);
        $this->assertRegExp('/Database Profiler/', $html);
        ob_end_clean();
    }

    public function testBootstrapNoDb()
    {
        (new Kernel('\Mmi\App\Bootstrap', 'NODB'))->run();
        $this->_testResponseAfterRun();
    }

    public function testBootstrapSession()
    {
        (new Kernel('\Mmi\App\Bootstrap', 'SESSION'))->run();
        $this->_testResponseAfterRun();
    }

    private function _testResponseAfterRun()
    {
        $response = \Mmi\App\FrontController::getInstance()->getResponse();
        $this->assertRegExp('/' . \Mmi\IndexController::DEFAULT_LABEL . '/', $response->getContent());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('text/html', $response->getType());
        return $response;
    }

}
