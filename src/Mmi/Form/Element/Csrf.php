<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Element ukrytego pola formularza
 */
class Csrf extends Hidden
{

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
