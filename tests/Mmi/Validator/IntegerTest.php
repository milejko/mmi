<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\Integer;

class IntegerTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Integer)->isValid(0));
        $this->assertTrue((new Integer)->isValid(41));
        $this->assertTrue((new Integer)->isValid(-12));
        //nie równe
        $this->assertFalse((new Integer)->isValid('-12,33'));
        $this->assertFalse((new Integer)->isValid('0a'));
        $this->assertFalse((new Integer)->isValid(31.12));
    }

}
