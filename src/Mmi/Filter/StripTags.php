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
 * Usunięcie tagów HTML
 * @method self setExceptions($exception)
 * @method string getExceptions()
 */
class StripTags extends \Mmi\Filter\FilterAbstract
{
    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this->setExceptions(current($options));
    }

    /**
     * Kasuje html'a
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        return strip_tags($value, $this->getExceptions() ? $this->getExceptions() : '');
    }
}
