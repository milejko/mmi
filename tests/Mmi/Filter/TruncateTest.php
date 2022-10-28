<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Truncate;

class TruncateTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals('ala ma kota a kot ma alę', (new Truncate)->setOptions([300])->filter('ala ma kota a kot ma alę'));
        $this->assertEquals('ala ma...', (new Truncate)->setOptions([10])->filter('ala ma kota a kot ma alę'));
        $this->assertEquals('ala ma...', (new Truncate)->setOptions([10, '...', false])->filter('ala ma kota a kot ma alę'));
        $this->assertEquals('ala ma kot...', (new Truncate)->setOptions([10, '...', true])->filter('ala ma kota a kot ma alę'));
        $this->assertEquals('zażó... cdn.', (new Truncate)->setOptions([4, '... cdn.'])->filter('zażółć żółtego żółwia'));
        $this->assertEquals('zażółć...', (new Truncate)->setOptions([8])->filter('zażółć żółtego żółwia'));
        $this->assertEquals('zażółć żółtego', (new Truncate)->setOptions([16, '', false])->filter('zażółć żółtego żółwia'));
    }
}
