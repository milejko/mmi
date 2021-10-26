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
 * Walidator adresu email
 */
class Phone extends ValidatorAbstract
{

    /**
     * Komunikat błędu
     */
    const INVALID = 'validator.phone.message';

    /**
     * Sprawdza czy tekst jest numerem telfonu
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        //dopasowanie telefonu
        if (preg_match('/([0-9\+\(])([0-9\- \)]+)([0-9]+)/i', $value)) {
            return true;
        }
        return $this->_error(static::INVALID);
    }

}
