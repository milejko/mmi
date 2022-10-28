<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\EmailAddress;

class EmailAddressTest extends \PHPUnit\Framework\TestCase
{
    public function testIsValid()
    {
        //równe
        $this->assertTrue((new EmailAddress)->isValid('test@example.com'));
        $this->assertTrue((new EmailAddress)->isValid('a.test@a.com.pl'));
        $this->assertTrue((new EmailAddress)->isValid('a+test@a.com.pl'));
        //nie równe
        $this->assertFalse((new EmailAddress)->isValid('xyz'));
        $this->assertFalse((new EmailAddress)->isValid('xyz@'));
        $this->assertFalse((new EmailAddress)->isValid('@xyz.pl'));
        $this->assertFalse((new EmailAddress)->isValid(null));
    }
}
