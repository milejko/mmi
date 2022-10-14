<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\Numeric;

class NumericTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Numeric)->isValid(412));
        $this->assertTrue((new Numeric)->isValid(412.32));
        $this->assertTrue((new Numeric)->isValid(-318.14));
        //nie równe
        $this->assertFalse((new Numeric)->isValid('-318,14'));
        $this->assertFalse((new Numeric)->isValid('abc'));
        $this->assertFalse((new Numeric)->isValid(null));
    }

}
