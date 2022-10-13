<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Filter;

use Mmi\Filter\Replace;

class ReplaceTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('xeti', (new Replace(['y', 'x']))->filter('yeti'));
        $this->assertEquals('yeti,,,test', (new Replace(['.', ',']))->filter('yeti...test'));
        $this->assertEquals('ółw', (new Replace(['Ż', '']))->filter('Żółw'));
    }

}
