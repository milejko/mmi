<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Filter;

use Mmi\Filter\IsEmpty;

class IsEmptyTest extends \PHPUnit\Framework\TestCase
{

    public function testFilter()
    {
        $this->assertEquals(false, (new IsEmpty)->filter('abc'));
        $this->assertEquals(false, (new IsEmpty)->filter([1,2]));
        $this->assertEquals(true, (new IsEmpty)->filter(''));
        $this->assertEquals(true, (new IsEmpty)->filter([]));
        $this->assertEquals(true, (new IsEmpty)->filter(0));
        $this->assertEquals(false, (new IsEmpty)->filter(12));
    }

}
