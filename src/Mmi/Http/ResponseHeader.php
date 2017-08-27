<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

/**
 * Klasa nagłówka odpowiedzi
 * @method ResponseHeader setName(string $name) ustawia nazwę
 * @method ResponseHeader setValue(string $value) ustawia wartość
 * @method ResponseHeader setReplace(boolean $replace) ustawia zastąpienie nagłówka
 * @method string getName() pobiera nazwę
 * @method string getValue() pobiera wartość
 * @method boolean getReplace() pobiera zstąpienie
 */
class ResponseHeader extends \Mmi\OptionObject
{

    /**
     * Metoda wysyłająca nagłówek
     */
    public function send()
    {
        //wysyłanie nagłówka
        header($this->getName() . ($this->getValue() ? ': ' . $this->getValue() : ''), (bool) $this->getReplace());
    }

    /**
     * Metoda wysyłająca nagłówek i przerywająca działanie aplikacji
     */
    public function sendAndExit()
    {
        $this->send();
        //wyjście z aplikacji
        exit;
    }

}
