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
 * Walidacja listy adresów
 */
class EmailAddressList extends ValidatorAbstract
{
    /**
     * Komunikat błędu
     */
    public const INVALID = 'validator.emailAddressList.message';

    /**
     * Sprawdza czy tekst jest e-mailem
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        $emails = explode(((false !== strpos($value, ',')) ? ',' : ';'), $value);
        //iteracja po mailach
        foreach ($emails as $email) {
            //niepoprawny email
            if (!(new EmailAddress())->isValid($email)) {
                return $this->_error(static::INVALID);
            }
        }
        return true;
    }
}
