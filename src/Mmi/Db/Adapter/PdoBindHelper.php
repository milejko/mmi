<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db\Adapter;

/**
 * Klasa generująca kolejne klucze bind
 *
 * @deprecated since 3.8 to be removed in 4.0
 */
class PdoBindHelper
{

    /**
     * Indeks powiązań
     * @var integer
     */
    private static $_bindIndex = 1000000;

    /**
     * Zwraca kolejny klucz do binda
     * @return string
     */
    public static function generateBindKey()
    {
        //generowanie kolejnego klucza bind
        return str_replace([0, 1, 2, 3, 4, 5, 6, 7, 8, 9], ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'], self::$_bindIndex++);
    }

}
