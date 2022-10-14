<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\NotEmpty;

class NotEmptyTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new NotEmpty)->isValid(0));
        $this->assertTrue((new NotEmpty)->isValid('a'));
        $this->assertTrue((new NotEmpty)->isValid(1));
        $this->assertTrue((new NotEmpty)->isValid([1]));
        //nie równe
        $this->assertFalse((new NotEmpty)->isValid(new \stdClass()));
        $this->assertFalse((new NotEmpty)->isValid(' '));
        $this->assertFalse((new NotEmpty)->isValid(''));
        $this->assertFalse((new NotEmpty)->isValid(null));
        $this->assertFalse((new NotEmpty)->isValid([]));
    }

}
