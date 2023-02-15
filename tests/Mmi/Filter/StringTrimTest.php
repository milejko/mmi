<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\StringTrim;

class StringTrimTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals('test', (new StringTrim())->filter('test '));
        $this->assertEquals('ala, ma, kota', (new StringTrim([' ,.']))->filter(' ala, ma, kota. '));
    }
}
