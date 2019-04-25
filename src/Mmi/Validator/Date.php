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
 * Walidator daty
 *
 * @deprecated since 3.9.0 to be removed in 4.0.0
 */
class Date extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'validator.date.message';

    /**
     * Walidacja daty
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        //data poprawna / niepoprawna
        return strtotime($value) ? true : $this->_error(self::INVALID);
    }

}
