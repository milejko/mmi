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
 * Test adapterów pdo bazy danych
 */
class PdoMysqlTest extends \PHPUnit\Framework\TestCase
{

    private $_db;

    public function setUp()
    {
        $cfg = new \Mmi\Db\DbConfig;
        $cfg->user = 'test';
        $cfg->name = 'test';
        $cfg->driver = 'mysql';
        $this->_db = new PdoMysql($cfg);
        try {
            $this->_db->connect();
        } catch (\PDOException $e) {
            $this->_db = null;
        }
    }

    public function testSelectSchema()
    {
        if (!$this->_db) {
            return;
        }
        $this->assertInstanceOf('\Mmi\Db\Adapter\PdoMysql', $this->_db->selectSchema('no-matter-in-mysql'));
    }

}
