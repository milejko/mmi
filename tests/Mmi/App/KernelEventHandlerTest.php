<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\App;

use Mmi\App\KernelEventHandler;

/**
 * Test handler błędów
 */
class KernelEventHandlerTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testErrorHandler()
    {
        1 / 0;
    }

    public function testShutdownHandler()
    {
        //czyszczenie odpowiedzi
        \Mmi\App\FrontController::getInstance()->setResponse(new \Mmi\Http\Response);
        $this->assertNull(KernelEventHandler::shutdownHandler(), 'Shutdown handler not returning null');
    }

    /**
     * 
     * @throws \Mmi\App\KernelException
     */
    public function testExceptionHandler()
    {
        KernelEventHandler::exceptionHandler(new \Exception('test'));
        $this->assertEquals('<html><body><h1>Error 500</h1><p>Something went wrong</p></body></html>', \Mmi\App\FrontController::getInstance()->getResponse()->getContent());
        \Mmi\App\FrontController::getInstance()->getResponse()->setTypePlain();
        KernelEventHandler::exceptionHandler(new \Mmi\App\KernelException);
        $this->assertStringStartsWith('Error 500', \Mmi\App\FrontController::getInstance()->getResponse()->getContent());
        \Mmi\App\FrontController::getInstance()->getResponse()->setTypeJson();
        KernelEventHandler::exceptionHandler(new \Mmi\App\KernelException);
        $this->assertEquals('{"status":500,"error":"something went wrong"}', \Mmi\App\FrontController::getInstance()->getResponse()->getContent());
        \Mmi\App\FrontController::getInstance()->getResponse()->setContent('');
    }

}
