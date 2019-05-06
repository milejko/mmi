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
 * Walidator json
 */
class Json extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'validator.json.message';

    /**
     * Walidacja jsona
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        try {
            if (null === \json_decode($value, true)) {
                return $this->_error(static::INVALID);
            }
        } catch (\Exception $e) {
            return $this->_error(static::INVALID);
        }
        return true;
    }

}
