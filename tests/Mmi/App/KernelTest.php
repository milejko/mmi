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
 * Test kernela aplikacji
 */
class KernelTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @expectedException \Mmi\App\KernelException
     */
    public function testFailBootstrap()
    {
        (new Kernel('stdClass', 'DEV'));
    }

    public function testrBootstrap()
    {
        $this->assertInstanceOf('\Mmi\App\Kernel', new Kernel('\Mmi\App\Bootstrap', 'DEV'));
    }

}
