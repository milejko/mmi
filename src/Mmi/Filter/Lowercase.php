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
 * Konwersja do małych liter
 */
class Lowercase extends \Mmi\Filter\FilterAbstract
{
    /**
     * Zmniejsza wszystkie litery w ciągu
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        //null jeśli nie string
        return is_string($value) ? mb_strtolower((string) $value, mb_detect_encoding((string) $value)) : null;
    }
}
