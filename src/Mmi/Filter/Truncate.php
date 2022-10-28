<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Filter;

/**
 * Przycięcie ciągu znaków
 *
 * @method self setLength($length)
 * @method getLength()
 * @method self setEnding($ending) końce linii
 * @method string getEnding()
 * @method self setBoundary($boundary) czy kończyć na pełnym wyrazie
 * @method boolean getBoundary()
 */
class Truncate extends \Mmi\Filter\FilterAbstract
{
    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this->setLength(current($options) ? (int) current($options) : 80)
                ->setEnding(next($options) !== false ? current($options) : '...')
                ->setBoundary(next($options) !== false ? true : false);
    }

    /**
     * Obcina ciąg do zadanej długości
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        $length = $this->getLength();
        //dostateczna długość
        if (mb_strlen($value, mb_detect_encoding($value)) < $length) {
            return $value;
        }
        //wykrycie encodingu
        $encoding = mb_detect_encoding($value);
        //standardowe skracanie
        if ($this->getBoundary()) {
            return mb_substr($value, 0, $length, $encoding) . $this->getEnding();
        }
        //skracanie do słowa
        if (false !== $lastSpace = mb_strrpos($value = mb_substr($value, 0, $length, $encoding), ' ')) {
            $value = mb_substr($value, 0, $lastSpace, $encoding);
        }
        $value .= $this->getEnding();
        return $value;
    }
}
