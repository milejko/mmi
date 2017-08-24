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
 * Test bootstrapa command line
 */
class BootstrapCliTest extends \PHPUnit\Framework\TestCase
{

    public function testStandardBootstrap()
    {
        (new Kernel('\Mmi\App\BootstrapCli', 'DEFAULT'))->run();
        $response = \Mmi\App\FrontController::getInstance()->getResponse();
        $this->assertNull($response->getContent());
        $this->assertEquals(200, $response->getCode());
        $this->assertEquals('text/html', $response->getType());
    }

}
