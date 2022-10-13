<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Validator;

use Mmi\Validator\Postal;

class PostalTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Postal)->isValid('00-000'));
        $this->assertTrue((new Postal)->isValid('01-511'));
        $this->assertTrue((new Postal)->isValid('03-312'));
        //nie równe
        $this->assertFalse((new Postal)->isValid('xaasss'));
        $this->assertFalse((new Postal)->isValid('000-00'));
    }

}
