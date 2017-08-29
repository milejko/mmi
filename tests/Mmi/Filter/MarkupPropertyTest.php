<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Filter;

use Mmi\Filter\MarkupProperty;

class MarkupPropertyTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('test1', (new MarkupProperty)->filter('test#1'));
        $this->assertEquals('xxx', (new MarkupProperty)->filter('x\'x\'x'));
        $this->assertEquals('xx', (new MarkupProperty)->filter('x,x'));
        $this->assertEquals('xx', (new MarkupProperty)->filter('x`x'));
        $this->assertEquals('xx', (new MarkupProperty)->filter('x"x'));
    }

}
