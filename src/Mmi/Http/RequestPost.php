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
class RequestPost extends \Mmi\DataObject
{

    /**
     * Konstruktor
     * @param array $post dane z POST
     */
    public function __construct(array $post = [])
    {
        $this->_data = $post;
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
