<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class Translate extends HelperAbstract
{

    /**
     * Metoda główna, zwraca swoją instancję
     * @return \Mmi\Mvc\ViewHelper\Translate
     */
    public function translate()
    {
        return $this;
    }

    /**
     * Tłumaczy wejściowy ciąg znaków
     * @return string
     */
    public function _($key)
    {
        return \App\Registry::$translate->_($key);
    }

}
