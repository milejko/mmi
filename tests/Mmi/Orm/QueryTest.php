<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test;

use Mmi\Orm\Query;

class QueryTest extends \PHPUnit\Framework\TestCase
{

    public function setUp()
    {
        $r1 = new \Mmi\Orm\CacheRecord;
        $r1->id = 'test1';
        $r1->data = 'test1-data';
        $r1->ttl = 1;
        $r1->save();
        $r2 = new \Mmi\Orm\CacheRecord;
        $r2->id = 'test2';
        $r2->data = 'test2-data';
        $r2->ttl = 1;
        $r2->save();
        $r3 = new \Mmi\Orm\CacheRecord;
        $r3->id = 'test3';
        $r3->data = 'test3-data';
        $r3->ttl = 1;
        $r3->save();
    }

    public function testNew()
    {
        $this->assertInstanceOf('\Mmi\Orm\Query', new Query('mmi_cache'));
    }

    public function testQuery()
    {
        $this->assertInstanceOf('\Mmi\Orm\RecordCollection', (new Query('mmi_cache'))->find());
    }

    /**
     * @expectedException \Mmi\Orm\OrmException
     */
    public function testCall()
    {
        (new Query('mmi_cache'))->inexistentMethod();
    }

    public function testCount()
    {
        $this->assertEquals(0, (new TestQuery)->whereAnotherColumn()->equals('non-existent')->count());
        $this->assertEquals(3, (new Query('mmi_cache'))->where('id')->like('test%')->count());
    }

    public function testLimitOffset()
    {
        $this->assertInstanceOf('\Mmi\Orm\Record', $r = (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->orderDesc('id')->findFirst());
        $this->assertEquals('test3', $r->id);
        $this->assertInstanceOf('\Mmi\Orm\Record', $r = (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->orderAsc('id')->findFirst());
        $this->assertEquals('test1', $r->getPk());
        $this->assertInstanceOf('\Mmi\Orm\RecordCollection', $rc = (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->orderAsc('id')->find());
        $this->assertEquals(3, $rc->count());
        $this->assertInstanceOf('\Mmi\Orm\RecordCollection', $rc = (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->orderAsc('id')->limit(2)->find());
        $this->assertEquals(2, $rc->count());
        $this->assertInstanceOf('\Mmi\Orm\RecordCollection', $rc = (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->offset(2)->limit(3)->orderAsc('id')->find());
        $this->assertEquals(1, $rc->count());
    }

    public function testOrder()
    {
        $this->assertSame(['test1' => 'test1', 'test2' => 'test2', 'test3' => 'test3'], (new \Mmi\Orm\CacheQuery)
                ->where('id')->like('test%')
                ->orderDesc('ttl')
                ->orderAsc('id')
                ->findPairs('id', 'id'));
        $this->assertSame(['test1' => 'test1', 'test2' => 'test2', 'test3' => 'test3'], (new \Mmi\Orm\CacheQuery)
                ->where('id')->like('test%')
                ->orderDesc('ttl')
                ->andQuery((new \Mmi\Orm\CacheQuery)->orderAsc('id'))
                ->andQuery((new \Mmi\Orm\CacheQuery)->orderAsc('id'))
                ->orderAsc('id')
                ->findPairs('id', 'id'));
        $record = (new \Mmi\Orm\CacheQuery)
            ->where('id')->like('test%')
            ->orderAsc('RAND()')
            ->findFirst();
        $this->assertTrue(in_array($record->id, ['test1', 'test2', 'test3']));
        $this->assertEquals(0, (new TestQuery)->andQuery((new TestQuery)->orderAsc('id'))->count());
    }

    public function testFindMinMax()
    {
        $this->assertEquals('test3', (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->findMax('id'));
        $this->assertEquals('test1', (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->findMin('id'));
    }

    public function testFindSum()
    {
        $this->assertEquals(3, (new \Mmi\Orm\CacheQuery)->where('id')->like('test%')->findSum('ttl'));
    }

    public function testMergeQueries()
    {
        $this->assertEquals(3, (new \Mmi\Orm\CacheQuery)->whereQuery((new \Mmi\Orm\CacheQuery)->where('id')->like('test%'))->count());
        $this->assertEquals(3, (new \Mmi\Orm\CacheQuery)->andQuery((new \Mmi\Orm\CacheQuery)->where('id')->like('test%'))->count());
        $this->assertEquals(3, (new \Mmi\Orm\CacheQuery)->orQuery((new \Mmi\Orm\CacheQuery)->where('id')->like('test%'))->count());
    }

    public function testGroupBy()
    {
        $this->assertEquals(1, (new Query('mmi_cache'))->where('id')->like('test%')->groupBy('id')->count());
        $this->assertEquals(3, (new Query('mmi_cache'))->where('id')->like('test%')->groupBy('ttl')->count());
        $this->assertEquals(1, (new Query('mmi_cache'))->where('id')->like('test%')->groupBy('ttl')->groupBy('id')->count());
    }

    public function testFind()
    {
        $r = new \Mmi\Orm\CacheRecord('test1');
        $this->assertEquals('test1', $r->getPk());
        $this->assertEquals(2, (new Query('mmi_cache'))->where('id')->like('test%')->andQuery((new Query('mmi_cache'))->where('id')->equals('test1')->orField('id')->equals('test2'))->count());
        $this->assertEquals('test1-data', (new \Mmi\Orm\CacheQuery)->findPk('test1')->data);
        $this->assertSame(['1'], (new Query('mmi_cache'))->where('id')->equals(['test1', 'test2', 'test3'])->findUnique('ttl'));
        $this->assertNull((new Query('mmi_cache'))->findPk('inexistent-id'));
        $this->assertInstanceOf('\Mmi\Orm\RecordCollection', (new TestQuery)->joinLeft('mmi_cache')->on('id')->find());
    }

    public function testJoin()
    {
        $this->assertSame(['test1' => 'test1'], (new \Mmi\Orm\CacheQuery)->where('id')->equals('test1')
                ->join('mmi_cache', 'mmi_cache', 'cache1')->on('id')
                ->join('mmi_cache', 'cache1', 'cache2')->on('id')
                ->findPairs('cache1.id', 'cache2.id'));

        $this->assertSame(['test1' => 'test1'], (new \Mmi\Orm\CacheQuery)->where('id')->equals('test1')
                ->join('mmi_cache', 'mmi_cache', 'cache1')->on('id')
                ->andQuery((new \Mmi\Orm\CacheQuery)->join('mmi_cache', 'mmi_cache', 'cache2')->on('id'))
                ->findPairs('cache1.id', 'cache2.id'));

        $this->assertSame(['test1' => 'test1'], (new \Mmi\Orm\CacheQuery)->where('id')->equals('test1')
                ->join('mmi_cache', 'mmi_cache', 'cache1')->on('id')
                ->join('mmi_cache', 'cache1', 'cache2')->on('id')
                ->findPairs('cache1.id', 'cache2.id'));
    }

    public function testResets()
    {
        $query = (new \Mmi\Orm\CacheQuery)->whereTtl()->greaterOrEquals(13)
            ->andFieldId()->equals(13)
            ->andFieldData()->like('taaas')
            ->groupByTtl()
            ->offset(1)
            ->limit(1);

        $this->assertEquals(3, $query->resetOrder()->resetWhere()->resetGroupBy()->count());
    }

    public function testQueryCompileHash()
    {
        $this->assertEquals('fdaea49f25dd99e002e568cb25ebb06a', (new \Mmi\Orm\CacheQuery)->where('ttl')->equals(1)
                ->join('mmi_cache')->on('id')->getQueryCompileHash());
        $this->assertEquals('03c3dbd53e5e63552144ba0a5fded14a', (new \Mmi\Orm\CacheQuery)->where('ttl')->equals(1)
                ->joinLeft('mmi_cache')->on('id')->getQueryCompileHash());
    }

}
