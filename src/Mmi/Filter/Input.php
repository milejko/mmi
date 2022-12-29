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
 * Filtruje znaki specjalne
 */
class Input extends \Mmi\Filter\FilterAbstract
{
    /**
     * Filtracja pól typu input text
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        return htmlspecialchars((string) $value);
    }
}
