<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\Date;

class DateTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Date)->isValid('2012-01-01 10:31:14'));
        $this->assertTrue((new Date)->isValid('01/01/2012 9:31:14'));
        //nie równe
        $this->assertFalse((new Date)->isValid(''));
        $this->assertFalse((new Date)->isValid('{xyz}'));
    }
}
