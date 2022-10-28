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
class Csrf extends Hidden
{
    /**
     * Walidator CSRF
     * @var \Mmi\Validator\Csrf
     */
    private $_validator;

    /**
     * Ignorowanie tego pola, pole obowiązkowe, automatyczna walidacja
     */
    public function __construct($name)
    {
        parent::__construct($name);
        //generowanie hasha
        $this//->setIgnore()
            ->setRequired()
            ->addValidator($this->_validator = new \Mmi\Validator\Csrf(['name' => $name]));
    }

    /**
     * Buduje pole
     * @return string
     */
    public function fetchField()
    {
        //ustawianie wartości
        $this->setValue($this->_validator->generateHash());
        return parent::fetchField();
    }
}
