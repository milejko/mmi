<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Tests\Mmi\Validator;

use Mmi\Validator\Iban;

class IbanTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Iban)->isValid('CY17 0020 0128 0000 0012 0052 7600'));
        $this->assertTrue((new Iban)->isValid('CH93 0076 2011 6238 5295 7'));
        $this->assertTrue((new Iban)->isValid('MU17 BOMM 0101 1010 3030 0200 000M UR'));
        $this->assertTrue((new Iban)->isValid('PL60 1020 1026 0000 0422 7020 1111'));
        $this->assertTrue((new Iban)->setCountry('PL')
            ->isValid('60 1020 1026 0000 0422 7020 1111'));
        //nie równe
        $this->assertFalse((new Iban)->isValid(null));
        $this->assertFalse((new Iban)->isValid('xyz'));
        $this->assertFalse((new Iban)->isValid('PL60 1020 1026 0000 0422 7020 1112'));
        $this->assertFalse((new Iban)->isValid('MU17 BOMM 0101 1010 3030 0200 000M US'));
    }

}
