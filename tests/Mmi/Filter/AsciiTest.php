<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Filter;

use Mmi\Filter\Ascii;

class AsciiTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('ala ma kota', (new Ascii)->filter('ala ma kota'));
        $this->assertEquals('_+|,/-', (new Ascii)->filter('!@#$%^&*()_+{}:"|<>?,./;\'\[]=-'));
        $this->assertEquals('zolw', (new Ascii)->filter('żółw'));
        $this->assertEquals('swinke', (new Ascii)->filter('świnkę'));
        $this->assertEquals('Shojgu zayavil ob okonchanii grazhdanskoj vojny v Sirii', (new Ascii)->filter('Шойгу заявил об окончании гражданской войны в Сирии'));
        $this->assertEquals('grosster', (new Ascii)->filter('größter'));
    }

}
