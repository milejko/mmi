<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Tests\Validator;

use Mmi\Validator\EmailAddressList;

class EmailAddressListTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new EmailAddressList)->isValid('test@example.com;a.test@a.com.pl;a+test@a.com.pl'));
        //nie równe
        $this->assertFalse((new EmailAddressList)->isValid('xyz,xyz@'));
    }

}
