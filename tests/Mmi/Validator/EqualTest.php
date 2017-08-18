<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Validator;

use Mmi\Validator\Equal;

/**
 * Test walidatora równości
 */
class EqualTest extends \PHPUnit\Framework\TestCase
{

    public function testIsValid()
    {
        //równe
        $this->assertTrue((new Equal)->setValue(10)
                ->isValid(10));
        $this->assertTrue((new Equal)->setValue(true)
                ->isValid(1));
        $this->assertTrue((new Equal)->setValue(true)
                ->isValid(true));
        $this->assertTrue((new Equal)->setValue(false)
                ->isValid(0));
        $this->assertTrue((new Equal)->setValue(false)
                ->isValid(false));
        $this->assertTrue((new Equal)->setValue(null)
                ->isValid(null));
        $this->assertTrue((new Equal)->setValue(null)
                ->isValid(false));
        $this->assertTrue((new Equal)->setValue(null)
                ->isValid(''));
        $this->assertTrue((new Equal)->setValue('test')
                ->isValid('test'));
        $this->assertTrue((new Equal)->setValue(new \stdClass())
                ->isValid(new \stdClass()));

        //nierówne
        $this->assertFalse((new Equal)->setValue(10)
                ->isValid(9));
        $this->assertFalse((new Equal)->setValue(true)
                ->isValid(false));
        $this->assertFalse((new Equal)->setValue('test')
                ->isValid('test1'));
        $this->assertFalse((new Equal)->setValue(new \stdClass())
                ->isValid(new Equal));
    }

    public function testSetOptions()
    {
        $customMessage = 'custom error';
        $customErrorValidator = new Equal([true, $customMessage]);
        $this->assertFalse($customErrorValidator->isValid(false));
        $this->assertEquals($customMessage, $customErrorValidator->getMessage());
    }

}
