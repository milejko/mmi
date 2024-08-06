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
 * Ascii + spacje na -
 */
class Url extends \Mmi\Filter\FilterAbstract
{
    /**
     * Klasa filtracji tekstów do url
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        if (!is_array($value)) {
            $ascii = new \Mmi\Filter\Ascii();
            return trim(preg_replace('!\-+!', '-', preg_replace('/[^\p{L}\p{N}]/u', '-', strtolower($ascii->filter((string) $value)))), '-');
        }
        foreach ($value as $key => $val) {
            $value[$key] = $this->filter($val);
        }
        return $value;
    }
}
