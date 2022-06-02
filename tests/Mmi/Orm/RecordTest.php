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

/**
 * @group infra
 */
class RecordTest extends \PHPUnit\Framework\TestCase
{

    public function testNew()
    {
        $this->expectException(\Mmi\Orm\RecordNotFoundException::class);
        new \Mmi\Orm\CacheRecord('surely-inexistent-id');
    }

    public function testIsModified()
    {
        $cr = new \Mmi\Orm\CacheRecord;
        $cr->id = 'test';
        $cr->ttl = 1;
        $cr->data = 'test';
        $this->assertTrue($cr->save());
        $this->assertFalse($cr->isModified('ttl'));
        $cr->ttl = 2;
        $this->assertTrue($cr->isModified('ttl'));
    }

    public function testSave()
    {
        $tr = new \Mmi\Test\TestRecord;
        $tr->camelCase = 'test1';
        $tr->anotherColumn = 'test2';
        $this->assertTrue($tr->save());
        $this->assertNull($tr->nullColumn);
        $this->assertEquals(1, $tr->id);
    }

}
