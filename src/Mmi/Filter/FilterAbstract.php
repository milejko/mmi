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
 * Abstrakcyjna klasa filtra
 */
abstract class FilterAbstract extends \Mmi\OptionObject
{

    /**
     * Zwraca przefiltrowaną wartość
     * @param mixed $value
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    abstract public function filter($value);
}
