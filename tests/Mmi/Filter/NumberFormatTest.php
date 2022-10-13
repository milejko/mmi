<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Filter;

use Mmi\Filter\NumberFormat;

class NumberFormatTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals('13 256,11', (new NumberFormat)->filter(13256.11));
        $this->assertEquals('13.256,10000', (new NumberFormat([1, ',', '.', true, 5]))->filter(13256.11));
        $this->assertEquals('13.256,11000', (new NumberFormat([2, ',', '.', true, 5]))->filter(13256.11));
        $this->assertEquals('13:256.110', (new NumberFormat([2, '.', ':', true, 3]))->filter(13256.11));
    }

}
