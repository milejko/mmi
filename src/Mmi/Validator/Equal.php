<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
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
 */
class Equal extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'Wprowadzona wartość nie jest poprawna';

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
