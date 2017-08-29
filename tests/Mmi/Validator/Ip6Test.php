<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Validator;

use Mmi\Validator\Ip6;

class Ip6Test extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Ip6)->isValid('2001:0db8:0a0b:12f0:0000:0000:0000:0001'));
        $this->assertTrue((new Ip6)->isValid('2001:db8:a0b:12f0::1'));
        $this->assertTrue((new Ip6)->isValid('3731:54:65fe:2::a7'));
        //nie równe
        $this->assertFalse((new Ip6)->isValid(':::::'));
        $this->assertFalse((new Ip6)->isValid('3731:54:65fe:::a7'));
        $this->assertFalse((new Ip6)->isValid('127.0.0.'));
        $this->assertFalse((new Ip6)->isValid(null));
    }

}
