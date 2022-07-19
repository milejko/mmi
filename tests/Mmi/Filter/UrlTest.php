<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Filter;

use Mmi\Filter\Url;

class UrlTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        //pozytywne
        $this->assertEquals('abc-defg', (new Url)->filter('AbC dEfg'));
        $this->assertEquals('10', (new Url)->filter(10));
        $this->assertEquals('', (new Url)->filter(null));
        $this->assertEquals('', (new Url)->filter('-'));
        $this->assertEquals('ala-ma-kota', (new Url)->filter('Ala ma kota!'));
        $this->assertEquals('nie-programuj-nocami-w-c', (new Url)->filter('Nie programuj nocami w C++'));
        $this->assertEquals('zwirek-i-muchomorek', (new Url)->filter('„Żwirek i Muchomorek”'));
        $this->assertEquals('azzcsc', (new Url)->filter('   ążźćść'));
        $this->assertEmpty((new Url)->filter('/-^-^-/'));
        $this->assertEquals('ya-lyublyu-php', (new Url)->filter('Я люблю PHP :)'));
        //negatywne
        $this->assertNotEquals('Test', (new Url)->filter('TesT'));
        $this->assertEmpty((new Url)->filter('\/\/\/\/'));
        //tablice
        $this->assertSame(['ala', 'ma', 'kota'], (new Url)->filter(['Ala', 'ma', 'kota!']));
        $this->assertSame(['test', '11', ['', 'alc']], (new Url)->filter(['Test', 11, ['!!!', 'AŁĆ']]));
    }

}
