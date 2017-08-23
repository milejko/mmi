<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Db\Adapter;

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

    public function setUp()
    {
        $db = new \Mmi\Db\Adapter\PdoSqlite(\App\Registry::$config->db);
        $db->delete('mmi_cache');
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $db->selectSchema('test'));
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $db->setDefaultImportParams());
        $this->_db = new \Mmi\Db\Adapter\PdoSqlite(\App\Registry::$config->db);
    }

    public function testQuote()
    {
        $this->assertEquals('13', $this->_db->quote(13));
        $this->assertEquals('\'13\'', $this->_db->quote('13'));
        $this->assertEquals('\'test\'', $this->_db->quote('test'));
        $this->assertEquals('true', $this->_db->quote(true));
        $this->assertEquals('false', $this->_db->quote(false));
    }

    public function testGetConfig()
    {
        $this->assertInstanceOf('\Mmi\Db\DbConfig', $this->_db->getConfig());
    }

    public function testPrepareSequenceName()
    {
        $this->assertEquals('mmi_cache_id_seq', $this->_db->prepareSequenceName('mmi_cache'));
    }

    /**
     * @expectedException \Mmi\Db\DbException
     */
    public function testInvalidQuery()
    {
        //exception
        $this->_db->query('INVALID-QUERY');
    }

    /**
     * @expectedException \Mmi\Db\DbException
     */
    public function testInvalidQueryData()
    {
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
            $this->assertEquals($value, $this->_db->fetchRow('SELECT ' . $this->_db->quote($value) . ' as test')['test']);
        }
    }

    /**
     * @expectedException \Mmi\Db\DbException
     */
    public function testInexistentMethod()
    {
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
        $this->assertNull($this->_db->getProfiler());
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoAbstract', $this->_db->setProfiler(new \Mmi\Db\DbProfiler));
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
        $this->assertEquals(['mmi_cache', 'mmi_changelog', 'mmi_session'], $this->_db->tableList());
    }

}
