<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Form\Element;

use Mmi\Form\FormException;
use Mmi\OptionObject;

/**
 * Element checkbox
 */
class Checkbox extends ElementAbstract
{
    //szablon pola
    public const TEMPLATE_FIELD = 'mmi/form/element/checkbox';

    /**
     * Konstruktor ustawia kolejność i opcje
     * @param string $name
     * @throws FormException
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setRenderingOrder(['fetchBegin', 'fetchField', 'fetchLabel', 'fetchDescription', 'fetchErrors', 'fetchEnd'])
            ->setLabelPostfix('');
    }

    /**
     * Ustawia zaznaczenie
     * @param bool $checked
     * @return OptionObject
     */
    public function setChecked($checked = true)
    {
        return (bool)$checked ? $this->setOption('checked', '') : $this->unsetOption('checked');
    }

    /**
     * Czy zaznaczone
     * @return bool
     */
    public function isChecked()
    {
        return $this->issetOption('checked');
    }
}
