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

    //szablon pola
    const TEMPLATE_FIELD = 'mmi/form/element/hidden';

    /**
     * Ignorowanie tego pola, pole obowiązkowe, automatyczna walidacja
     */
    public function __construct($name)
    {
        parent::__construct($name);
        $this->setIgnore()
            ->setRequired()
            ->addValidator(new \Mmi\Validator\Csrf(['name' => $name]));
    }

    /**
     * Buduje pole
     * @return string
     */
    public function fetchField()
    {
        foreach ($this->getValidators() as $validator) {
            if (!($validator instanceof \Mmi\Validator\Csrf)) {
                continue;
            }
            $this->setValue($validator->generateHash());
            break;
        }
        return parent::fetchField();
    }

}
