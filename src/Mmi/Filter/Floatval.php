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
 * Floatval
 */
class Floatval extends \Mmi\Filter\FilterAbstract
{
    /**
     * Wycina wszystko poza liczbami
     * @param mixed $value wartość
     * @return mixed
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     */
    public function filter($value)
    {
        return (float)$value;
    }
}
