<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Klasa guzika
 */
class Button extends ElementAbstract
{
    
    //szablon pola
    const TEMPLATE_FIELD = 'mmi/form/element/button';

    /**
     * Ignorowanie tego pola, inna kolejnośc renderowania
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setIgnore();
        $this->setRenderingOrder(['fetchBegin', 'fetchField', 'fetchErrors', 'fetchEnd']);
    }

    /**
     * Buduje pole
     * @return string
     */
    public function fetchField()
    {
        //labelka jako value
        if ($this->getLabel()) {
            $this->setValue($this->getLabel());
        }
        //opcje do widoku
        $this->view->_htmlOptions = $this->_getHtmlOptions();
        //render szablonu
        return $this->view->renderTemplate(static::TEMPLATE_FIELD);
    }

}
