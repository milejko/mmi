<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\App;

use Mmi\App\KernelException;

/**
 * Test kernela aplikacji
 */
class KernelExceptionTest extends \PHPUnit\Framework\TestCase
{

    public function testFailBootstrap()
    {
        $this->assertStringStartsWith(' test:', (new KernelException('test'))->getExtendedMessage(), 'Message is not proper');
    }

}
