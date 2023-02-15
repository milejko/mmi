<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Alnum;

class AlnumTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals('abc', (new Alnum())->filter('abc'));
        $this->assertEquals('alamakota', (new Alnum())->filter('ala ma kota'));
        $this->assertEquals('', (new Alnum())->filter('!@#$%^&*()_+{}:"|<>?,./;\'\[]=-'));
    }
}
