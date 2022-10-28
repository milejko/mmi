<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Filter;

use Mmi\Filter\Round;

class RoundTest extends \PHPUnit\Framework\TestCase
{
    public function testFilter()
    {
        $this->assertEquals(313.57, (new Round([2]))->filter(313.567));
        $this->assertEquals(313.567, (new Round([3]))->filter(313.567));
        $this->assertEquals(313.6, (new Round([1]))->filter(313.567));
        $this->assertEquals(314, (new Round([0]))->filter(313.567));
        $this->assertEquals(313, (new Round([0]))->filter(313.467));
    }
}
