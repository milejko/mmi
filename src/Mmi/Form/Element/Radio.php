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
 * Element radiobutton
 */
class Radio extends ElementAbstract
{
    //szablon pola
    const TEMPLATE_FIELD = 'mmi/form/element/radio';

    /**
     * Konstruktor
     * @param string $name
     */
    public function __construct($name)
    {
        $this->addClass('form-control');
        parent::__construct($name);
    }
}
