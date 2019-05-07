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
 * Walidator IP6
 */
class Ip6 extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'validator.ip6.message';

    /**
     * Walidacja IPv6
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        //poprawny IP6
        if (preg_match('/^(?>(?>([a-f0-9]{1,4})(?>:(?1)){7}|(?!(?:.*[a-f0-9](?>:|$)){8,})((?1)(?>:(?1)){0,6})?::(?2)?)|(?>(?>(?1)(?>:(?1)){5}:|(?!(?:.*[a-f0-9]:){6,})(?3)?::(?>((?1)(?>:(?1)){0,4}):)?)?(25[0-5]|2[0-4][0-9]|1[0-9]{2}|[1-9]?[0-9])(?>\.(?4)){3}))$/iD', $value)) {
            return true;
        }
        return $this->_error(static::INVALID);
    }

}
