<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Db\Adapter;

use Mmi\App\AppProfiler;
use Mmi\App\Kernel;
use Mmi\Db\DbConfig;

/**
 * Test adapterów pdo bazy danych
 */
class PdoTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Adapter DB
     * @var \Mmi\Db\Adapter\PdoAbstract
     */
    private $_db;

    public static function setUpBeforeClass(): void
    {
        //require_once BASE_PATH . '/tests/data/config-cache.php';
        //(new Kernel('\Mmi\App\Bootstrap', 'CACHE'));
    }

    public function setUp(): void
    {
        $dbConfig = new DbConfig;
        $dbConfig->driver = 'sqlite';
        $dbConfig->host = BASE_PATH . '/var/test-db.sqlite';
        $db = new \Mmi\Db\Adapter\PdoSqlite($dbConfig);
        $db->delete('mmi_cache');
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $db->setDefaultImportParams());
        $this->_db = new \Mmi\Db\Adapter\PdoSqlite($dbConfig);
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf('\Mmi\Db\DbConfig', $this->_db->getConfig());
    }

    public function testInvalidQuery()
    {
        $this->expectException(\PDOException::class);
        //exception
        $this->_db->query('INVALID-QUERY');
    }

    public function testInvalidQueryData()
    {
        $this->expectException(\Mmi\Db\DbException::class);
        //exception
        $this->_db->query('INSERT INTO ' . $this->_db->prepareTable('mmi_cache') . ' (' . $this->_db->prepareField('id') . ', ' . $this->_db->prepareField('data') . ', ' . $this->_db->prepareField('ttl') . ') VALUES (?, ?, ?)', ['x', 'y']);
    }

    public function testQuery()
    {
        $this->assertInstanceOf('\PDOStatement', $this->_db->query('SELECT 1'));
    }

    public function testLastInsertId()
    {
        $this->assertEquals(0, $this->_db->lastInsertId());
        $this->assertInstanceOf('\PDOStatement', $this->_db->query('INSERT INTO ' . $this->_db->prepareTable('mmi_cache') . ' (' . $this->_db->prepareField('"id"') . ', ' . $this->_db->prepareField('data') . ', ' . $this->_db->prepareField('ttl') . ') VALUES (?, ?, ?)', ['ooo', 'ooo', false]));
        $this->assertEquals(1, $this->_db->lastInsertId());
    }

    public function testFetchAll()
    {
        foreach ($this->_db->fetchAll('SELECT 1 as test') as $row) {
            $this->assertEquals(1, $row['test']);
        }
    }

    public function testFetchRow()
    {
        foreach ([null, 1, '1', 'test', 17.31] as $value) {
            $this->assertEquals($value, $this->_db->fetchRow('SELECT "' . $value . '" as test')['test']);
        }
    }

    public function testInexistentMethod()
    {
        $this->expectException(\Mmi\Db\DbException::class);
        $this->_db->surelyNonExistentMethod();
    }

    public function testInvalidTransaction()
    {
        $this->assertFalse($this->_db->commit());
        $this->assertFalse($this->_db->rollBack());
    }

    public function testInsertUpdateDelete()
    {
        $this->assertTrue($this->_db->beginTransaction());
        $this->assertEquals(1, $this->_db->insert('mmi_cache', ['id' => 'ppp', 'data' => 'ppp', 'ttl' => time()]));
        $this->assertEquals([['COUNT(*)' => '1']], $this->_db->select('COUNT(*)', 'mmi_cache'));
        $this->assertEquals(1, $this->_db->delete('mmi_cache', 'WHERE id = :id', ['id' => 'ppp']));
        $this->assertEquals([['COUNT(*)' => '0']], $this->_db->select('COUNT(*)', 'mmi_cache'));
        $this->assertTrue($this->_db->rollBack());
        $this->assertTrue($this->_db->beginTransaction());
        $this->assertEquals(1, $this->_db->insert('mmi_cache', ['id' => 'ppp', 'data' => 'ppp', 'ttl' => time()]));
        $this->assertEquals(1, $this->_db->update('mmi_cache', ['data' => 'ccc', 'ttl' => time()], 'WHERE id = :id', ['id' => 'ppp']));
        $this->assertEquals(1, $this->_db->update('mmi_cache', [], 'WHERE id = :id', ['id' => 'ppp']));
        $this->assertEquals([['COUNT(*)' => '1']], $this->_db->select('COUNT(*)', 'mmi_cache'));
        $this->assertEquals(1, $this->_db->delete('mmi_cache', 'WHERE id = :id', ['id' => 'ppp']));
        $this->assertEquals([['COUNT(*)' => '0']], $this->_db->select('COUNT(*)', 'mmi_cache', '', '', '', 1));
        $this->assertEquals([], $this->_db->select('COUNT(*)', 'mmi_cache', '', '', '', 1, 1));
        $this->assertTrue($this->_db->commit());
    }

    public function testInsertAll()
    {
        $this->assertEquals(0, $this->_db->delete('mmi_cache'));
        $this->assertEquals(2, $this->_db->insertAll('mmi_cache', [[], ['id' => 'q', 'data' => 'ppp', 'ttl' => time()], ['id' => 'r', 'data' => 'ppp', 'ttl' => time()]]));
        $this->assertEquals([['COUNT(*)' => '2']], $this->_db->select('COUNT(*)', 'mmi_cache'));
    }

    public function testGetSetProfiler()
    {
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $this->_db->setProfiler(new \Mmi\Db\DbProfiler(new AppProfiler)));
        $this->assertInstanceOf('\Mmi\Db\DbProfiler', $this->_db->getProfiler());
    }

    public function testTableInfo()
    {
        $this->assertEquals(['id' => [
                'dataType' => 'text',
                'maxLength' => null,
                'null' => false,
                'default' => null
            ],
            'data' =>
            ['dataType' => 'text',
                'maxLength' => null,
                'null' => false,
                'default' => null
            ],
            'ttl' =>
            ['dataType' => 'integer',
                'maxLength' => null,
                'null' => false,
                'default' => null
            ]], $this->_db->tableInfo('mmi_cache'));
    }

    public function testTableList()
    {
        $this->assertEquals(['mmi_cache', 'mmi_changelog', 'mmi_session', 'test', 'sqlite_sequence'], $this->_db->tableList());
    }

}
