<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

/**
 * Klasa zapytania post
 */
class RequestGet extends \Mmi\DataObject
{

    /**
     * Konstruktor
     * @param array $get dane z GET
     */
    public function __construct(array $get = [])
    {
        $this->_data = $get;
    }

    /**
     * Sprawdza pustość post
     * @return boolean
     */
    public function isEmpty()
    {
        return empty($this->_data);
    }

}
