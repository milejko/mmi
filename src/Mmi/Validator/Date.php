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
 * Walidator daty
 */
class Date extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'Wprowadzona wartość nie jest poprawną datą';

    /**
     * Walidacja daty
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        //data niepoprawna
        if (!strtotime($value)) {
            return $this->_error(self::INVALID);
        }
        return true;
    }

}
