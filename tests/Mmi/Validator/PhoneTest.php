<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2018 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Test\Validator;

use Mmi\Validator\Phone;

/**
 * Test walidatora numeru telefonu
 */
class PhoneTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Phone
     */
    protected $_phoneValidator;

    /**
     *
     */
    public function setUp()
    {
        $this->_phoneValidator = new Phone();
    }

    /**
     * @return array
     */
    public function getValidValues()
    {
        return [
            '+48 123 456 789',
            '123 456 789',
            '81 123 45 67'
        ];
    }

    /**
     * Testowanie poprawnych numerów telefonu
     */
    public function testIsValid()
    {
        foreach ($this->getValidValues() as $value) {
            $this->assertTrue(
                $this->_phoneValidator->isValid($value),
                sprintf('Value "%s" should be valid', $value)
            );
        }
    }

    /**
     * @return array
     */
    public function getInvalidValues()
    {
        return [
            '123',
            'abc',
            '+48 123 456 78',
            '+48 123 456 7890',
            '123 456 78',
            '123 456 7890',
            '81 123 45 6',
            '81 123 45 678',
        ];
    }

    /**
     * Testowanie niepoprawnych numerów telefonu
     */
    public function testIsInvalid()
    {
        foreach ($this->getInvalidValues() as $value) {
            $this->assertFalse(
                $this->_phoneValidator->isValid($value),
                sprintf('Value "%s" should be invalid', $value)
            );
        }
    }

}
