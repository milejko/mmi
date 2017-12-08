<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Db\Adapter;

use Mmi\Db\Adapter\PdoPgsql;

/**
 * Test PdoPgsql
 */
class PdoPgsqlTest extends \PHPUnit\Framework\TestCase
{
    
    /*CONST host = 'localhost'; // host
    CONST user = 'postgres'; // user
    CONST password = 'postgrespassword'; // password
    CONST name = 'mmi_phpunit'; // database name
    CONST schema = 'public'; // schema
    CONST tblName = 'test'; // table name
    CONST driver = 'pgsql'; // driver
    CONST port = '5432'; // port
    
    
    private $_db;

    public function setUp()
    {
        $cfg = new \Mmi\Db\DbConfig;
        $cfg->host = self::host;
        $cfg->user = self::user;
        $cfg->password = self::password;
        $cfg->name = self::name;
        //$cfg->schema = self::schema; // jeśli nie podamy schematu mamy większe pokrycie w PHPUnit
        $cfg->driver = self::driver;
        $cfg->port = self::port;
        $this->_db = new PdoPgsql($cfg);
        try {
            $this->_db->connect();
        } catch (\PDOException $e) {
            $this->_db = null;
        }
    }
    
    public function testConnect()
    {
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoPgsql', $this->_db->connect());
    }

    public function testSetDefaultImportParams()
    {
        $this->assertEquals($this->_db, $this->_db->setDefaultImportParams());
    }
    
    public function testPrepareField()
    {
        $inputField = '"';
        $this->assertEquals($inputField, $this->_db->prepareField($inputField));
    }
    
    public function testPrepareTable()
    {
        // ustawiamy losowy ciąg znaków
        $someString = 'test'.rand(1, 100000);
        $this->assertEquals('"'.$someString.'"', $this->_db->prepareTable($someString));
    }
    
    public function testPrepareLimit()
    {
        $limit = 0;
        $offset = 0;
        $this->assertEquals(false, $this->_db->prepareLimit($limit, $offset));
            
        $limit = 1;
        $offset = 0;
        $this->assertEquals(' LIMIT '.intval($limit), $this->_db->prepareLimit($limit, $offset));
        
        $limit = 1;
        $offset = 1;
        $this->assertEquals(' LIMIT ' . intval($limit) . ' OFFSET ' . intval($offset), $this->_db->prepareLimit($limit, $offset));
    }
    
    public function testPrepareNullCheck()
    {
        $inputField = 'testowe';
        $this->assertEquals($inputField.' ISNULL', $this->_db->prepareNullCheck($inputField, true));
        // sprawdzenie, jeśli nie podamy jawnie drugiego parametru
        $this->assertEquals($inputField.' ISNULL', $this->_db->prepareNullCheck($inputField));
        $this->assertEquals($inputField.' NOTNULL', $this->_db->prepareNullCheck($inputField, false));
    }
    
    public function testTableInfo()
    {
        $array = $this->_db->tableInfo(self::tblName);
        $this->assertEquals('integer', $array['id']['dataType']);
    }
    
    public function testTableList()
    {
        $tableList = $this->_db->tableList(self::schema);
        foreach ($this->_db->tableList(self::schema) as $key => $val) {
            $this->assertEquals(self::tblName, $val);
        }
    }
    
    public function testPrepareIlike()
    {
        $fieldName = 'fieldNameTest';
        $expected = 'CAST(' . $fieldName . ' AS text) ILIKE';
        $this->assertEquals($expected, $this->_db->prepareIlike($fieldName));
    }*/
    
}
