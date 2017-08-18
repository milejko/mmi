<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

/**
 * Urlencode
 */
class Urlencode extends \Mmi\Filter\FilterAbstract
{

    /**
     * Filtr kodujący znaki z urlencode
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        return urlencode($value);
    }

}
