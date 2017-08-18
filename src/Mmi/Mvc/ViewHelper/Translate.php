<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
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
        return $this->view->getTranslate()->_($key);
    }

}
