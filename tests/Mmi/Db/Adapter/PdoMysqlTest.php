<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Db\Adapter;

use Mmi\Db\Adapter\PdoMysql;

/**
 * Test PdoMysql
 */
class PdoMysqlTest extends \PHPUnit\Framework\TestCase
{
    
    CONST HOST = 'localhost';
    CONST PORT = '3306';
    CONST USER = 'root';
    CONST PASSWORD = '';
    CONST DB_NAME = 'test';
    CONST TABLE_NAME = 'test';

    private $_db;

    public function setUp(): void
    {
        $cfg = new \Mmi\Db\DbConfig;
        $cfg->user = self::USER;
        $cfg->password = self::PASSWORD;
        $cfg->name = self::DB_NAME;
        $cfg->driver = 'mysql';
        $cfg->port = self::PORT;
        $this->_db = new PdoMysql($cfg);
        try {
            $this->_db->connect();
        } catch (\PDOException $e) {
            $this->_db = null;
        }
    }

    public function testSelectSchema()
    {
        $this->_createTable();
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoMysql', $this->_db->selectSchema(self::TABLE_NAME));
        $this->_deleteTable();
    }
    
    public function testSetDefaultImportParams()
    {
        $this->assertEquals($this->_db, $this->_db->setDefaultImportParams());
    }
    
    public function testConnect()
    {
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoMysql', $this->_db->connect());
    }
    
    // utworzenie tabeli tymczasowej na potrzeby testów (jeśli nie istnieje)
    private function _createTable()
    {
        if (!$this->_db->query('CREATE TABLE IF NOT EXISTS `' . self::TABLE_NAME. '` (
                `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `test_name` varchar(255) NOT NULL,
                `test_value` char NOT NULL
            );')) {
            return;
        }
    }
    
    // usunięcie tabeli tymczasowej
    private function _deleteTable()
    {
        $this->_db->delete(self::TABLE_NAME);
    }


    public function testTableInfo()
    {
        $this->assertEquals([
            'id' => [
                'dataType' => 'int',
                'maxLength' => null,
                'null' => false,
                'default' => null
            ],
            'test_name' => [
                'dataType' => 'varchar',
                'maxLength' => '255',
                'null' => false,
                'default' => ''
            ],
            'test_value' => [
                'dataType' => 'char',
                'maxLength' => '1',
                'null' => false,
                'default' => ''
            ]
        ], $this->_db->tableInfo(self::TABLE_NAME));
    }
    
    public function testPrepareTable()
    {
        $this->assertEquals('`nazwa`', $this->_db->prepareTable('nazwa'));
    }

    public function testInsertAll()
    {
        $this->assertEquals(2, $this->_db->insertAll(self::TABLE_NAME, [['test_name' => 't1', 'test_value' => '1'], [], ['test_name' => 't2', 'test_value' => '2']]));
        $this->assertSame(['COUNT(*)' => '2'], $this->_db->fetchRow('SELECT COUNT(*) FROM `' .self::TABLE_NAME. '` WHERE `test_name` = "t1" OR `test_name` = "t2"'));
    }
    
    public function testPrepareField()
    {
        $tests = [
            'nazwa' => '`nazwa`',
            'RAND()' => 'RAND()',
            '`nazwa`' => '`nazwa`',
        ];
        foreach ($tests as $key => $val) {
            $this->assertEquals($val, $this->_db->prepareField($key));
        }
    }
    
    public function testTableList()
    {
        foreach ($this->_db->tableList(self::TABLE_NAME) as $row) {
            $this->assertEquals(self::TABLE_NAME, $row);
        }
    }
    
    public function testPrepareNullCheck()
    {
        $fieldName = 'test_name';
        $this->assertEquals('ISNULL('.$fieldName.')', $this->_db->prepareNullCheck($fieldName));
    }
    
    public function testPrepareLike()
    {
        $fieldName = 'testowanie';
        $this->assertEquals($fieldName.' LIKE', $this->_db->prepareLike($fieldName));
    }
}
