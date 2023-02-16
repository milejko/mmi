<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Orm;

use Mmi\Orm\RecordCollection;

class RecordCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testRecordCollection()
    {
        $this->assertEquals(3, count($arr = (new \Mmi\Orm\CacheQuery())->find()->toArray()));
        $this->assertEquals(3, count($objArr = (new \Mmi\Orm\CacheQuery())->find()->toObjectArray()));
        foreach ($arr as $objectDumpedToArray) {
            $this->assertArrayHasKey('id', $objectDumpedToArray);
        }
        foreach ($objArr as $object) {
            $this->assertInstanceOf('\Mmi\Orm\CacheRecord', $object);
        }

        $collection = (new \Mmi\Orm\CacheQuery())->find();
        $clonedCollection = clone($collection);
        $this->assertSame($collection->toArray(), $clonedCollection->toArray());

        $this->assertSame(json_decode((new \Mmi\Orm\CacheQuery())->find()->toJson(), true), $arr);
        $this->assertEquals(3, (new \Mmi\Orm\CacheQuery())->find()->delete());
        $this->assertEquals(0, (new \Mmi\Orm\CacheQuery())->find()->delete());
    }
}
