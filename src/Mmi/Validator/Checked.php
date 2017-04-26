<?php

namespace Mmi\Validator;

/**
 * Walidator zaznaczenia checkboxa
 * 
 * @method self setElement(\Mmi\Form\Element\Checkbox $element) ustawia checkbox do jako wartość
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 * 
 * @method string getElement() pobiera wartość bazową
 * @method string getMessage() pobiera wiadomość
 */
class Checked extends ValidatorAbstract
{

    /**
     * Treść wiadomości
     */
    const INVALID = 'Pole wymaga zaznaczenia';

    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        return $this->setElement(current($options))
                ->setMessage(next($options));
    }

    /**
     * Walidacja zaznaczenia
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value)
    {
        //jeśli niezaznaczony
        if (!$this->getElement()->isChecked()) {
            return $this->_error(self::INVALID);
        }
        return true;
    }

}
