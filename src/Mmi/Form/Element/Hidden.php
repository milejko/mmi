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
 * Element ukrytego pola formularza
 */
class Hidden extends ElementAbstract
{
    //szablon pola
    public const TEMPLATE_FIELD = 'mmi/form/element/hidden';

    /**
     * Konstruktor zmienia kolejność renderowania
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setRenderingOrder(['fetchBegin', 'fetchField', 'fetchErrors', 'fetchEnd']);
    }
}
