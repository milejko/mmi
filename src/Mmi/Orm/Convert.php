<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

/**
 * Klasa konwersji
 *
 * @deprecated since 3.11 to be removed in 4.0
 */
class Convert
{

    /**
     * Konwertuje podkreślenia na camelcase
     * @param string $value
     * @return string
     */
    public static final function underscoreToCamelcase($value)
    {
        //używa callbacku
        return preg_replace_callback('/\_([a-z0-9])/', function ($matches) {
            return ucfirst($matches[1]);
        }, $value);
    }

    /**
     * Konwertuje camelcase na podkreślenia
     * @param string $value
     * @return string
     */
    public static final function camelcaseToUnderscore($value)
    {
        //używa callbacku
        return preg_replace_callback('/([A-Z])/', function ($matches) {
            return '_' . lcfirst($matches[1]);
        }, $value);
    }

}
