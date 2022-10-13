<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Validator;

use Mmi\Validator\StringLength;

/**
 * Test walidatora równości
 */
class StringLengthTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new StringLength([1, 3]))->isValid('xyz'));
        $this->assertTrue((new StringLength([1, 3]))->isValid('ślę'));
        $this->assertTrue((new StringLength([1, 3]))->isValid('ś'));
        $this->assertTrue((new StringLength)->isValid(new \stdClass()));
        //nie równe
        $this->assertFalse((new StringLength([1, 2]))->isValid('abc'));
        $this->assertFalse((new StringLength([1, 3]))->isValid(''));
        $this->assertFalse((new StringLength([1, 3]))->isValid('xyzabc'));
    }

}
