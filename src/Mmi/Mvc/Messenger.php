<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\Translate;

/**
 * Klasa wiadomości
 */
class Messenger
{
    const SESSION_SPACE = 'mmi-messenger';

    /**
     * Przestrzeń w sesji zarezerwowana dla wiadomości
     * @var \Mmi\Session\SessionSpace
     */
    private $_session;

    /**
     * @var Translate
     */
    private $translate;

    /**
     * Konstruktor pozwala zdefiniować nazwę przestrzeni w sesji
     */
    public function __construct(Translate $translate)
    {
        //injections
        $this->translate = $translate;
        $this->_session = new \Mmi\Session\SessionSpace(self::SESSION_SPACE);
    }

    /**
     * Dodaje wiadomość
     * @param string $message wiadomość
     * @param bool $type true - pozytywna, false - negatywna, brak - neutralna
     * @param array $vars zmienne
     * @return \Mmi\Message\Messenger
     */
    public function addMessage($message, $type = null, array $vars = [])
    {
        if ($type) {
            $type = 'success';
        } elseif (false === $type) {
            $type = 'error';
        }
        if (!is_array($this->_session->messages)) {
            $this->_session->messages = [];
        }
        $messages = $this->_session->messages;
        $messages[] = ['message' => $message, 'type' => $type, 'vars' => $vars];
        $this->_session->messages = $messages;
        return $this;
    }

    /**
     * Czy są jakieś wiadomości
     * @return boolean
     */
    public function hasMessages()
    {
        return (is_array($this->_session->messages) && !empty($this->_session->messages));
    }

    /**
     * Pobiera wiadomości
     * @return array
     */
    public function getMessages($clear = true)
    {
        $messages = [];
        if (is_array($this->_session->messages)) {
            $messages = $this->_session->messages;
        }
        //czyszczenie
        if ($clear) {
            $this->clearMessages();
        }
        return $messages;
    }

    /**
     * Czyści wiadomości
     * @return \Mmi\Message\Messenger
     */
    public function clearMessages()
    {
        $this->_session->unsetAll();
        return $this;
    }

    /**
     * Zlicza wiadomości
     * @return int
     */
    public function count()
    {
        return count($this->_session->messages);
    }

    /**
     * Przygotowuje przetłumaczoną wiadomość
     * @param array $message
     * @return string
     */
    public function prepareTranslatedMessage(array $message = [])
    {
        $translatedMessage = $this->translate->_($message['message']);
        array_unshift($message['vars'], $translatedMessage);
        return call_user_func_array('sprintf', $message['vars']);
    }

}
