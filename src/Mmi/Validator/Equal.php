<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Validator;

/**
 * Walidator równości
 * 
 * @method self setValue($value) ustawia wartość bazową
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 * 
 * @method string getValue() pobiera wartość bazową
 * @method string getMessage() pobiera wiadomość
 *
 * @deprecated since 3.9.0 to be removed in 4.0.0
 */
class Equal extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'validator.equal.message';

    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this->setValue(current($options))
            ->setMessage(next($options));
    }

    /**
     * Walidacja porówniania wartości
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        //wartość nierówna
        if ($this->getValue() != $value) {
            return $this->_error(self::INVALID);
        }
        return true;
    }

}
