<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Session;

/**
 * Klasa obsługi sesji w apc
 */
class ApcHandler implements \SessionHandlerInterface {
	
	/**
	 * Namespace sesji
	 */
	private $_namespace;

	/**
	 * Otwarcie sesji
	 * @param string $savePath
	 * @param string $sessionName
	 * @return boolean
	 */
	public function open($savePath, $sessionName) {
		$this->_namespace = 'sess-' . crc32($sessionName . $savePath) . '-';
		return true;
	}

	/**
	 * Odczyt danych do sesji
	 * @param string $id
	 * @return mixed
	 */
	public function read($id) {
		//pobieranie z apcu
		if (null === $data = \apcu_fetch($this->_namespace . $id)) {
			//nie może zwracać null
			return '';
		}
		//zwrot danych
		return $data;
	}

	/**
	 * Zapis danych w sesji
	 * @param string $id
	 * @param mixed $data
	 * @return boolean
	 */
	public function write($id, $data) {
		//puste dane
		if (!$data) {
			//czyszczenie
			\apcu_delete($this->_namespace . $id);
			return true;
		}
		//zapis danych
		\apcu_store($this->_namespace . $id, $data);
		return true;
	}

	/**
	 * Zamknięcie sesji (nie robi nic)
	 * @return boolean
	 */
	public function close() {
		return true;
	}

	/**
	 * Usunięcie sesji
	 * @param string $id
	 * @return boolean
	 */
	public function destroy($id) {
		//usuwanie danych
		\apcu_delete($this->_namespace . $id);
		return true;
	}

	/**
	 * Garbage collector
	 * @param integer $maxLifetime
	 * @return boolean
	 */
	public function gc($maxLifetime) {
		return true;
	}

}
