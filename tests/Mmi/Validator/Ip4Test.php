<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\Ip4;

class Ip4Test extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Ip4)->isValid('127.0.0.1'));
        $this->assertTrue((new Ip4)->isValid('0.0.0.0'));
        $this->assertTrue((new Ip4)->isValid('214.123.46.3'));
        //nie równe
        $this->assertFalse((new Ip4)->isValid('x.y.z.a'));
        $this->assertFalse((new Ip4)->isValid('512.0.0.0'));
        $this->assertFalse((new Ip4)->isValid('127.0.0.'));
        $this->assertFalse((new Ip4)->isValid(null));
    }

}
