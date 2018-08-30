<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Session;

use Mmi\Session\ApcHandler;

/**
 * Test handlera plikowego
 */
class ApcHandlerTest extends \PHPUnit\Framework\TestCase
{

    CONST FILE_PATH = BASE_PATH . '/var/session/sess-';

    public function testOpen()
    {
        $this->assertTrue((new ApcHandler())->open('test', 'test'));
    }

    public function testRead()
    {
        $fh = new ApcHandler();
        $this->assertEquals('', $fh->read('abc'));
        $sessionId = md5(microtime());
        $this->assertEquals('', $fh->read($sessionId));
    }

    public function testClose()
    {
        $this->assertTrue((new ApcHandler())->close());
    }

    public function testGc()
    {
        $fh = new ApcHandler();
        $this->assertTrue($fh->gc(0));
    }

}