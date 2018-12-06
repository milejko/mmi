<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Validator;

/**
 * Klasa pozwalająca tworzyć grupy walidacyjne
 *
 * @package Mmi\Validator
 */
class Sequence extends ValidatorAbstract
{
    /**
     * @var array
     */
    protected $validators;

    public function addValidator(ValidatorAbstract $validator)
    {
        $this->validators[] = $validator;
    }

    public function isValid($value)
    {
        $isValid = true;
        foreach ($this->validators as $validator) {
            $isValid = $validator->isValid($value);
            if (true !== $isValid) {
                $this->_error($validator->getMessage() ? $validator->getMessage() : $validator->getError());
                return $isValid;
            }
        }
        return $isValid;
    }
}
