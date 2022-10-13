<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Session;

use Mmi\Session\FileHandler;

/**
 * Test handlera plikowego
 */
class FileHandlerTest extends \PHPUnit\Framework\TestCase
{

    public function testOpen()
    {
        $this->assertTrue((new FileHandler())->open('test', 'test'));
    }

    public function testRead()
    {
        $fh = new FileHandler();
        $this->assertEquals('', $fh->read('abc'));
        $sessionId = md5(microtime());
        $this->assertEquals('', $fh->read($sessionId));
    }

    public function testWrite()
    {
        $fh = new FileHandler();
        $this->assertTrue($fh->write('abc', 'xxx'));
        $sessionId = md5(microtime());
        $this->assertTrue($fh->write($sessionId, 'xxx'));
        $this->assertEquals('xxx', $fh->read($sessionId));
        $this->assertTrue($fh->write($sessionId, 'xxx'));
        $this->assertEquals('xxx', $fh->read($sessionId));
        $this->assertTrue($fh->write($sessionId, null));
        $this->assertEquals(null, $fh->read($sessionId));
    }

    public function testClose()
    {
        $this->assertTrue((new FileHandler())->close());
    }

    public function testDestroy()
    {
        $fh = new FileHandler();
        $sessionId = md5(microtime());
        $this->assertTrue($fh->write($sessionId, 'xxx'));
        $this->assertTrue($fh->destroy('abc'));
        $this->assertTrue($fh->destroy($sessionId));
        $this->assertEquals(null, $fh->read($sessionId));
    }

    public function testGc()
    {
        $fh = new FileHandler();
        $sessionId = md5(microtime());
        $this->assertTrue($fh->write($sessionId, 'xxx'));
        $this->assertEquals('xxx', $fh->read($sessionId));
        $this->assertTrue($fh->gc(0));
        $this->assertEquals(null, $fh->read($sessionId));
        $this->assertTrue($fh->write($sessionId, 'abc'));
        $this->assertTrue($fh->gc(1));
        $this->assertEquals('abc', $fh->read($sessionId));
    }

}