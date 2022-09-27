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
 * Walidator liczba pomiędzy
 * 
 * @method self setFrom($from) ustawia od
 * @method self setTo($to) ustawia do
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 * 
 * @method integer getFrom() pobiera od
 * @method integer getTo() pobiera do
 * @method string getMessage() pobiera wiadomość 
 */
class NumberBetween extends ValidatorAbstract
{
    const INVALID = 'validator.numberBetween.message';
    const INVALID_MIN = 'validator.numberBetween.messageMin';
    const INVALID_MAX = 'validator.numberBetween.messageMax';

    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this->setFrom(current($options))
            ->setTo(next($options))
            ->setMessage(next($options));
    }

    /**
     * Walidacja liczb od-do
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        if (!is_numeric($value)) {
            return $this->_error(self::INVALID);
        }

        if ($this->getFrom() && $value < $this->getFrom()) {
            return $this->_error([self::INVALID_MIN, [$this->getFrom()]]);
        }

        if ($this->getTo() && $value > $this->getTo()) {
            return $this->_error([self::INVALID_MAX, [$this->getTo()]]);
        }
        return true;
    }

}
