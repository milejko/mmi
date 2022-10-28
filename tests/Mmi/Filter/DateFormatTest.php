<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\DateFormat;

class DateFormatTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals(date('Y/m/d'), (new DateFormat)->setOptions(['Y/m/d'])->filter(time()));
        $this->assertEquals('01.01.2015 00:00:00', (new DateFormat)->filter('2015-01-01'));
        $this->assertEquals('2015/01/01', (new DateFormat)->setOptions(['Y/m/d'])->filter('2015-01-01'));
    }
}
