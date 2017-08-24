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

}
