<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Validator;

use Mmi\Validator\Alnum;

class AlnumTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Alnum)->isValid('xyz'));
        $this->assertTrue((new Alnum)->isValid('żółw'));
        //nie równe
        $this->assertFalse((new Alnum)->isValid(new \stdClass()));
        $this->assertFalse((new Alnum)->isValid('{xyz}'));
    }

}
