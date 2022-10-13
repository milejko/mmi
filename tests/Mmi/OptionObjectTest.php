<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests;

use Mmi\OptionObject;

/**
 * Test obiektu opcji
 */
class OptionObjectTest extends \PHPUnit\Framework\TestCase
{

    public function testSetOptions()
    {
        $oo = new OptionObject;
        $oo->setB('b');
        $this->assertInstanceOf('\Mmi\OptionObject', $oo->setOptions(['a' => 'a'], true));
        $this->assertFalse($oo->issetOption('b'));
        $this->assertInstanceOf('\Mmi\OptionObject', $oo->setOptions(['c' => 'c'], false));
        $this->assertTrue($oo->issetOption('a'));
        $this->assertInstanceOf('\Mmi\OptionObject', $oo->setOptions(['d' => 'd']));
        $this->assertTrue($oo->issetOption('c'));
    }

    public function testSettersGetters()
    {
        $this->expectException(\Mmi\App\KernelException::class);
        $oo = new OptionObject;
        $this->assertInstanceOf('\Mmi\OptionObject', $oo->setOption('a', 'a')
                ->setB('b')
                ->setC('c')
                ->setD('d')
        );
        $this->assertTrue($oo->issetOption('a'));
        $this->assertTrue($oo->issetOption('b'));
        $this->assertTrue($oo->issetOption('c'));
        $this->assertTrue($oo->issetOption('d'));
        $this->assertInstanceOf('\Mmi\OptionObject', $oo->unsetOption('b')
            ->unsetC()
            );
        $this->assertFalse($oo->issetOption('b'));
        $this->assertTrue($oo->issetA());
        $this->assertFalse($oo->issetC());
        $oo->someMethod();
    }

}
