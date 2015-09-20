<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

class FlashMessenger {

	/**
	 * Przestrzeń w sesji zarezerwowana dla wiadomości
	 * @var \Mmi\Session\Space
	 */
	static protected $_session = null;

	/**
	 * Nazwa przestrzeni
	 * @var string
	 */
	private $_namespace;

	/**
	 * Konstruktor pozwala zdefiniować nazwę przestrzeni w sesji
	 */
	public function __construct($namespace = 'FlashMessenger') {
		$this->_namespace = $namespace;
		self::$_session = new \Mmi\Session\Space($this->_namespace);
	}

	/**
	 * Dodaje wiadomość
	 * @param string $message wiadomość
	 * @param bool $type true - pozytywna, false - negatywna, brak - neutralna
	 * @param array $vars zmienne
	 * @return \Mmi\FlashMessenger
	 */
	public function addMessage($message, $type = null, array $vars = []) {
		if ($type) {
			$type = 'success';
		} elseif (false === $type) {
			$type = 'error';
		}
		if (!is_array(self::$_session->messages)) {
			self::$_session->messages = [];
		}
		$messages = self::$_session->messages;
		$messages[] = ['message' => $message, 'type' => $type, 'vars' => $vars];
		self::$_session->messages = $messages;
		return $this;
	}

	/**
	 * Czy są jakieś wiadomości
	 * @return boolean
	 */
	public function hasMessages() {
		return (is_array(self::$_session->messages) && !empty(self::$_session->messages));
	}

	/**
	 * Pobiera wiadomości
	 * @return array
	 */
	public function getMessages($clear = true) {
		$messages = [];
		if (is_array(self::$_session->messages)) {
			$messages = self::$_session->messages;
		}
		//czyszczenie
		if ($clear) {
			$this->clearMessages();
		}
		return $messages;
	}

	/**
	 * Czyści wiadomości
	 * @return \Mmi\FlashMessenger
	 */
	public function clearMessages() {
		self::$_session->unsetAll();
		return $this;
	}

	/**
	 * Zlicza wiadomości
	 * @return int
	 */
	public function count() {
		return count(self::$_session->messages);
	}

}
