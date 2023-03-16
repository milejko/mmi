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
 * @method self setMessageMin($messageMin) ustawia własną min. wiadomość walidatora
 * @method self setMessageMax($messageMax) ustawia własną max. wiadomość walidatora
 *
 * @method integer getFrom() pobiera od
 * @method integer getTo() pobiera do
 * @method string getMessageMin() pobiera wiadomość min.
 * @method string getMessageMax() pobiera wiadomość max.
 */
class StringLength extends ValidatorAbstract
{
    /**
     * Komunikat niedostatecznej długości
     */
    public const INVALID_MIN = 'validator.stringLength.messageMin';
    public const INVALID_MAX = 'validator.stringLength.messageMax';

    /**
     * Ustawia opcje
     * @param array $options
     * @param bool $reset
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this
            ->setFrom(current($options))
            ->setTo(next($options))
            ->setMessageMin(next($options) ?: self::INVALID_MIN)
            ->setMessageMax(next($options) ?: self::INVALID_MAX);
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
            return $this->_error([$this->getMessageMin(), [$this->getFrom()]]);
        }
        //za długi
        if ($this->getTo() && mb_strlen($value) > $this->getTo()) {
            return $this->_error([$this->getMessageMax(), [$this->getTo()]]);
        }
        return true;
    }
}
