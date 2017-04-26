<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db\Adapter;

/**
 * Klasa generująca kolejne klucze bind
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
