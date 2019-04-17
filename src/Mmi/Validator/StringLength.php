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
 * Walidator długość ciągu pomiędzy
 * 
 * @method self setFrom($from) ustawia od
 * @method self setTo($to) ustawia do
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 *  
 * @method integer getFrom() pobiera od
 * @method integer getTo() pobiera do
 * @method string getMessage() pobiera wiadomość
 */
class StringLength extends ValidatorAbstract
{

    /**
     * Komunikat niedostatecznej długości
     */
    const INVALID = 'validator.stringLength.message';

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
     * Waliduje długość ciągu, długość zadana jest w opcjach (przy konstruktorze)
     * w tabeli postaci array(minimum, maksimum)
     * @param string $value
     * @return boolean
     */
    public function isValid($value)
    {
        //za krótki
        if ($this->getFrom() && mb_strlen($value) < $this->getFrom()) {
            return $this->_error(self::INVALID);
        }
        //za długi
        if ($this->getTo() && mb_strlen($value) > $this->getTo()) {
            return $this->_error(self::INVALID);
        }
        return true;
    }

}
