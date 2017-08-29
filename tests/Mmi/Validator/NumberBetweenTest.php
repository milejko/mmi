<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Validator;

use Mmi\Validator\NumberBetween;

class NumberBetweenTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new NumberBetween([2, 3]))->isValid(2));
        $this->assertTrue((new NumberBetween([2, 3]))->isValid(3));
        //nie równe
        $this->assertFalse((new NumberBetween([2, 3]))->isValid(null));
        $this->assertFalse((new NumberBetween([2, 3]))->isValid(4));
        $this->assertFalse((new NumberBetween([2, 3]))->isValid(1));
    }

}
