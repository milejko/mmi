<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Db;

use Mmi\Db\DbProfiler;

/**
 * Test profilera bazy danych
 */
class DbProfilerTest extends \PHPUnit\Framework\TestCase
{

    public function testCount()
    {
        $profiler = new DbProfiler;
        $this->assertEquals(0, $profiler->count());
        $this->assertInstanceOf('\Mmi\Db\DbProfiler', $profiler->event(new \PDOStatement, ['test' => 'test'], 0));
        $this->assertEquals(1, $profiler->count());
        $this->assertEquals(0, $profiler->elapsed());
        $this->assertCount(1, $profiler->get());
        $this->assertInstanceOf('\Mmi\Db\DbProfiler', $profiler->event(new \PDOStatement, [1], 31));
        $this->assertEquals(2, $profiler->count());
        $this->assertGreaterThan(30, $profiler->elapsed());
        $this->assertInstanceOf('\Mmi\Db\DbProfiler', $profiler->event(new \PDOStatement, [1], 31));
        $this->assertInstanceOf('\Mmi\Db\DbProfiler', $profiler->event(new \PDOStatement, [1], 31));
        $this->assertCount(4, $profiler->get());
    }

    public function testGet()
    {
        $this->assertEmpty((new DbProfiler)->get());
    }

}
