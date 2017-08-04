<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Element pole na adres e-mail
 */
class Email extends ElementAbstract
{

    /**
     * Rendering pola typu e-mail
     * @return string
     */
    public function fetchField()
    {
        $this->setValue((new \Mmi\Filter\Input)->filter($this->getValue()));
        return '<input type="email" ' . $this->_getHtmlOptions() . '/>';
    }

}
