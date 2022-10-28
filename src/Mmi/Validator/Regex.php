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
 * Walidator równości
 *
 * @method self setPattern($pattern) ustawia pattern
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 *
 * @method string getPattern() pobiera pattern
 * @method string getMessage() pobiera wiadomość
 */
class Regex extends ValidatorAbstract
{
    /**
     * Treść wiadomości
     */
    public const INVALID = 'validator.regex.message';

    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this->setPattern(current($options))
            ->setMessage(next($options));
    }

    /**
     * Walidacja za pomocą wyrażenia regularnego
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        //jeśli nie podano wzorca, to przyjmujemy, że pasuje
        if (is_null($this->getPattern()) || false === $this->getPattern()) {
            return true;
        }
        //błędny typ danych
        if (!is_string($value) && !is_int($value) && !is_float($value)) {
            return $this->_error(static::INVALID);
        }
        try {
            //badanie wyrażeniem
            $status = preg_match($this->getPattern(), $value);
        } catch (\Mmi\App\KernelException $e) {
            return $this->_error(static::INVALID);
        }
        return $status ? true : $this->_error(static::INVALID);
    }
}
