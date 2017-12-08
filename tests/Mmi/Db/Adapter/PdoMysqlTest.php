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
    
    /*CONST host = 'localhost';
    CONST upstreamHost = 'localhost';
    CONST user = 'root';
    CONST password = 'rooter';
    CONST name = 'mmi_fresh';
    CONST tblName = 'test';
    CONST driver = 'mysql';
    CONST port = '3306';
    CONST upstreamPort = '3306';
    
    
    private $_db;

    public function setUp()
    {
        $cfg = new \Mmi\Db\DbConfig;
        $cfg->user = self::user;
        $cfg->password = self::password;
        $cfg->name = self::name;
        $cfg->driver = self::driver;
        $cfg->port = self::port;
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
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoMysql', $this->_db->selectSchema(self::name));
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
        if (!$this->_connect()->query('CREATE TABLE IF NOT EXISTS `'.self::tblName.'` (
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
        $this->_connect()->delete(self::tblName);
    }
    
    // utworzenie połączenia na potrzeby testów
    private function _connect()
    {
        $cfg = new \Mmi\Db\DbConfig;
        $cfg->user = self::user;
        $cfg->password = self::password;
        $cfg->name = self::name;
        $cfg->driver = self::driver;
        $cfg->port = self::port;
        $connect = new PdoMysql($cfg);
        return $connect;
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
        ], $this->_db->tableInfo(self::tblName));
    }
    
    public function testPrepareTable()
    {
        $this->assertEquals('`nazwa`', $this->_db->prepareTable('nazwa'));
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
        foreach ($this->_db->tableList(self::name) as $row) {
            $this->assertEquals(self::tblName, $row);
        }
    }
    
    public function testPrepareNullCheck()
    {
        $fieldName = 'test_name';
        $this->assertEquals('ISNULL('.$fieldName.')', $this->_db->prepareNullCheck($fieldName));
    }
    
    public function testPrepareIlike()
    {
        $fieldName = 'testowanie';
        $this->assertEquals($fieldName.' LIKE', $this->_db->prepareIlike($fieldName));
    }*/
}
