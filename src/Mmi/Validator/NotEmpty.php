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
 * Walidator pustościv
 *
 * @deprecated since 3.9.0 to be removed in 4.0.0
 */
class NotEmpty extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'validator.notEmpty.message';

    /**
     * Walidacja niepustości
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_null($value) && !is_string($value) && !is_int($value) && !is_float($value) &&
            !is_bool($value) && !is_array($value)) {
            $this->_error(self::INVALID);
            return false;
        }
        if (is_string($value) && (('' === $value) || preg_match('/^\s+$/s', $value))) {
            $this->_error(self::INVALID);
            return false;
        } elseif (is_int($value) && (0 === $value)) {
            return true;
        } elseif (!is_string($value) && empty($value)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}
