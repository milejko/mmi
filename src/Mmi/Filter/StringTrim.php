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
 * Obcięcie
 *
 * @method self setExtras($extras)
 * @method string getExtras()
 */
class StringTrim extends \Mmi\Filter\FilterAbstract
{
    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this->setExtras(current($options));
    }

    /**
     * Usuwa spacę z końców ciągu znaków
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        $chars = ' ';
        if ($this->getExtras()) {
            $chars .= $this->getExtras();
        }
        return trim($value, $chars);
    }
}
