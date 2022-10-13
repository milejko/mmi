<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

use Mmi\Db\DbConfig;
use Mmi\Session\DbHandler;

/**
 * Test handlera plikowego
 */
class DbHandlerTest extends \PHPUnit\Framework\TestCase
{

    public function setUp(): void
    {
        $dbConfig = new DbConfig;
        $dbConfig->driver = 'sqlite';
        $dbConfig->host = BASE_PATH . '/var/test-db.sqlite';
        $db = new \Mmi\Db\Adapter\PdoSqlite($dbConfig);
        $db->delete('mmi_cache');
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $db->setDefaultImportParams());
    }

    public function testOpen()
    {
        $this->assertTrue((new DbHandler())->open('test', 'test'));
    }

    public function testRead()
    {
        $fh = new DbHandler();
        $this->assertEquals('', $fh->read('abc'));
        $sessionId = md5(microtime());
        $this->assertEquals('', $fh->read($sessionId));
    }

    public function testWrite()
    {
        $fh = new DbHandler();
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
        $this->assertTrue((new DbHandler())->close());
    }

    public function testDestroy()
    {
        $fh = new DbHandler();
        $sessionId = md5(microtime());
        $this->assertTrue($fh->write($sessionId, 'xxx'));
        $this->assertTrue($fh->destroy('abc'));
        $this->assertTrue($fh->destroy($sessionId));
        $this->assertEquals(null, $fh->read($sessionId));
    }

    public function testGc()
    {
        $fh = new DbHandler();
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