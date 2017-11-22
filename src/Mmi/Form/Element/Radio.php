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
     * Buduje etykietę pola
     * @return string
     */
    public function fetchLabel()
    {
        //brak labelki
        if (!$this->getOption('data-label')) {
            return;
        }
        $requiredClass = '';
        $required = '';
        //html znaku wymagania
        if ($this->getRequired()) {
            $requiredClass = ' class="required"';
            $required = '<span class="required">' . $this->getOption('data-requiredAsterisk') . '</span>';
        }
        //tłumaczenie labelki
        $label = $this->getOption('data-label');
        //rendering
        return '<label' . $requiredClass . '>' . $label . $this->getOption('data-labelPostfix') . $required . '</label>';
    }

}
