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
 * Element label
 */
class Label extends ElementAbstract
{
    /**
     * Konstruktor usuwa labelpostfix
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setLabelPostfix('')
            ->setIgnore();
    }

    /**
     * Buduje pole
     * @return string
     */
    public function fetchField()
    {
        return '';
    }
}
