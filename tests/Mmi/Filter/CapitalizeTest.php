<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Capitalize;

class CapitalizeTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals('Ala Ma Kota, A Kot Ma Alę', (new Capitalize())->filter('ala ma kota, a kot ma alę'));
        $this->assertEquals('Żółw Świnia Łódź Źdźbło Ósemka', (new Capitalize())->filter('żółw świnia łódź źdźbło ósemka'));
    }
}
