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
 * Element wielokrotny checkbox
 * @method self setMultioptions(array $options)
 * @method array getMultioptions()
 */
class MultiCheckbox extends ElementAbstract
{
    /**
     * Konstruktor ustawia nazwę bazową formularza
     * @param string $name
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setOption('containerClass', trim($this->getOption('containerClass')) . 'multi-box multi-checkbox');
    }

    /**
     * Buduje pole
     * @return string
     */
    public function fetchField()
    {
        //wartości pola
        $values = is_array($this->getValue()) ? $this->getValue() : [$this->getValue()];
        $html = '<ul id="' . ($baseId = $this->getId()) . '-list">';
        //filtr url
        $f = new \Mmi\Filter\Url();
        foreach ($this->getMultioptions() as $key => $caption) {
            //nowy checkbox
            $checkbox = new Checkbox($this->getBaseName() . '[]');
            //konfiguracja checkboxa
            $checkbox->setLabel($caption)
                ->setForm($this->_form)
                ->setValue($key)
                ->setId($baseId . '-' . $f->filter($key))
                ->setRenderingOrder(['fetchField', 'fetchLabel']);
            //zaznaczenia wartości
            if (in_array($key, $values)) {
                $checkbox->setChecked();
            }
            //wartość wyłączona
            if (strpos($key, ':disabled') !== false) {
                $checkbox->setValue('')
                    ->setDisabled();
            }
            $html .= '<li ' . ($checkbox->getDisabled() ? 'class="disabled" ' : '') . 'id="' . $checkbox->getId() . '-item' . '">' .
                $checkbox . '</li>';
        }
        return $html . '</ul>';
    }
}
