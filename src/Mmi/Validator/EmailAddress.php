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
class EmailAddress extends ValidatorAbstract
{

    /**
     * Komunikat niedostatecznej długości
     */
    const INVALID = 'Niepoprawny adres e-mail';

    /**
     * Sprawdza czy tekst jest e-mailem
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        //dopasowanie emaila
        if (preg_match('/^([*+!.&#$|\'\\%\/0-9a-z^_`{}=?~:-]+)@(([0-9a-z-]+\.)+[0-9a-z]{2,4})$/i', $value)) {
            return true;
        }
        return $this->_error(self::INVALID);
    }

}
