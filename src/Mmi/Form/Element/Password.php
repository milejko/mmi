<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Pole hasło
 */
class Password extends ElementAbstract
{

    /**
     * Buduje pole
     * @return string
     */
    public function fetchField()
    {
        return '<input type="password" ' . $this->_getHtmlOptions() . '/>';
    }

}
