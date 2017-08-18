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
 * Walidator numeryczny
 */
class Numeric extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'Wprowadzona wartość nie jest liczbą';

    /**
     * Walidacja liczb
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        //błąd
        if (!is_numeric($value)) {
            return $this->_error(self::INVALID);
        }
        return true;
    }

}
