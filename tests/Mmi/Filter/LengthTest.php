<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Length;

class LengthTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals(3, (new Length)->filter('abc'));
        $this->assertEquals(0, (new Length)->filter(''));
        $this->assertEquals(4, (new Length)->filter('żółw'));
        $this->assertEquals(2, (new Length)->filter(['a', 'b']));
        $this->assertNull((new Length)->filter(new \stdClass()));
    }
}
