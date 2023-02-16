<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Lowercase;

class LowercaseTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals('abc', (new Lowercase())->filter('AbC'));
        $this->assertEquals('', (new Lowercase())->filter(''));
        $this->assertEquals('żółw', (new Lowercase())->filter('ŻÓŁw'));
        $this->assertNull((new Lowercase())->filter(['a', 'b']));
        $this->assertNull((new Lowercase())->filter(new \stdClass()));
    }
}
