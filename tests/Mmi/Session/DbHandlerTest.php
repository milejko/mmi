<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Session;

use Mmi\Session\DbHandler;
use Mmi\App\Kernel;

/**
 * Test handlera plikowego
 */
class DbHandlerTest extends \PHPUnit\Framework\TestCase
{

    public static function setUpBeforeClass(): void
    {
        require_once 'data/config-cache.php';
        (new Kernel('\Mmi\App\Bootstrap', 'CACHE'));
    }

    public function setUp(): void
    {
        $db = new \Mmi\Db\Adapter\PdoSqlite(\App\Registry::$config->db);
        $db->delete('mmi_cache');
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $db->selectSchema('test'));
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $db->setDefaultImportParams());
        $this->_db = new \Mmi\Db\Adapter\PdoSqlite(\App\Registry::$config->db);
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