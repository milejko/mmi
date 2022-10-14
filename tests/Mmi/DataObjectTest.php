<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests;

use Mmi\DataObject;

/**
 * Test obiektu danych
 */
class DataObjectTest extends \PHPUnit\Framework\TestCase
{

    public function testSetParams()
    {
        $do = new DataObject;
        $do->b = 'b';
        $this->assertInstanceOf('\Mmi\DataObject', $do->setParams(['a' => 'a'], true));
        $this->assertFalse(isset($do->b));
    }

    public function testSettersGetters()
    {
        $do = new DataObject;
        $do->a = 'a';
        $do->b = 'b';
        $this->assertTrue(isset($do->a));
        $this->assertTrue(isset($do->b));
        $this->assertFalse(isset($do->c));
        unset($do->b);
        $this->assertFalse(isset($do->b));
        $this->assertEquals('a', $do->a);
    }

}
