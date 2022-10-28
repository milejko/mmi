<?php

namespace Mmi\Validator;

/**
 * Walidator zaznaczenia checkboxa
 *
 * @method self setMessage($message) ustawia własną wiadomość walidatora
 *
 * @method \Mmi\Form\Element\Checkbox getElement() pobiera checkbox
 * @method string getMessage() pobiera wiadomość
 */
class Checked extends ValidatorAbstract
{
    /**
     * Treść wiadomości
     */
    public const INVALID = 'validator.checked.message';

    /**
     * Ustawia element
     * @param \Mmi\Form\Element\Checkbox $element
     * @return self
     */
    public function setElement(\Mmi\Form\Element\Checkbox $element = null)
    {
        return $this->setOption('element', $element);
    }

    /**
     * Ustawia opcje
     * @param array $options
     * @return self
     */
    public function setOptions(array $options = [], $reset = false)
    {
        $element = current($options);
        //jeśli element jest ustawiony
        if ($element instanceof \Mmi\Form\Element\Checkbox) {
            $this->setElement($element);
        }
        return $this->setMessage(next($options));
    }

    /**
     * Walidacja zaznaczenia
     * @param mixed $value wartość
     * @return boolean
     */
    public function isValid($value = null)
    {
        //jeśli niezaznaczony
        if ($this->getElement() && !$this->getElement()->isChecked()) {
            return $this->_error(static::INVALID);
        }
        return true;
    }
}
