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
 *
 * @deprecated since 3.9.0 to be removed in 4.0.0
 */
class NumberBetween extends ValidatorAbstract
{

    /**
     * Treść błędu 
     */
    const INVALID = 'validator.numberBetween.message';

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
        //czy liczba
        if (!is_numeric($value)) {
            return $this->_error(self::INVALID);
        }
        //sprawdzamy dolny zakres
        if ($this->getFrom() !== null && $value < $this->getFrom()) {
            return $this->_error(self::INVALID);
        }
        //sprawdzamy górny zakres
        if ($this->getTo() !== null && $value > $this->getTo()) {
            return $this->_error(self::INVALID);
        }
        return true;
    }

}
