<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Mvc\ViewHelper;

use Mmi\Mvc\ViewHelper\Escape,
    \Mmi\Filter\Escape as EscapeFilter;

class EscapeTest extends \PHPUnit\Framework\TestCase
{

    public function testEscape()
    {
        $this->assertEquals((new EscapeFilter)->filter('test'), (new Escape)->escape('test'));
        $this->assertEquals((new EscapeFilter)->filter('<script>alert();</script>'), (new Escape)->escape('<script>alert();</script>'));
    }

}
