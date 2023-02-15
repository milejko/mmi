<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\Regex;

class RegexTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Regex(['/[a-z0-9]{2,5}/']))->isValid('aac'));
        $this->assertTrue((new Regex(['/[a-z0-9]{2,5}/']))->isValid('mp3'));
        $this->assertTrue((new Regex())->isValid('mp3'));
        //nie równe
        $this->assertFalse((new Regex(['/[a-z]/']))->isValid('0'));
        $this->assertFalse((new Regex(['/[a-z]/']))->isValid(new \stdClass()));
    }
}
