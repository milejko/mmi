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
 * Klasa oszczędnej obsługi sesji w plikach
 */
class FileHandler implements \SessionHandlerInterface {
	
	/**
	 * Namespace sesji
	 */
	private $_namespace;
	
	/**
	 * Dane w sesji
	 * @var mixed 
	 */
	private $_data;

	/**
	 * Otwarcie sesji
	 * @param string $savePath
	 * @param string $sessionName
	 * @return boolean
	 */
	public function open($savePath, $sessionName) {
		$this->_namespace = BASE_PATH . '/var/session/sess-';
		return true;
	}

	/**
	 * Odczyt danych do sesji
	 * @param string $id
	 * @return mixed
	 */
	public function read($id) {
		//niepoprawne ID
		if (!$this->_validate($id)) {
			return '';
		}
		//pobieranie z pliku i zapis do rejestru
		try {
			return ($this->_data = file_get_contents($this->_namespace . $id));
		} catch (\Exception $e) {
			//nic
		}
		//pusta sesja
		return '';
	}

	/**
	 * Zapis danych w sesji
	 * @param string $id
	 * @param mixed $data
	 * @return boolean
	 */
	public function write($id, $data) {
		//dane nie uległy zmianie
		if ($data == $this->_data) {
			return true;
		}
		//niepoprawne ID
		if (!$this->_validate($id)) {
			return true;
		}
		//puste dane
		if (!$data) {
			//próba czyszczenie jeśli plik znaleziony
			try {
				file_exists($this->_namespace . $id) && unlink($this->_namespace . $id);
			} catch (\Exception $e) {
				//nic
			}
			return true;
		}
		//zapis danych
		file_put_contents($this->_namespace . $id, $data);
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
		//niepoprawne ID
		if (!$this->_validate($id)) {
			return true;
		}
		//próba usunięcia danych
		try {
			file_exists($this->_namespace . $id) && unlink($this->_namespace . $id);
		} catch (\Exception $e) {
			//nic
		}
		return true;
	}
	
	/**
	 * Garbage collector
	 * @param integer $maxLifetime
	 * @return boolean
	 */
	public function gc($maxLifetime) {
		//iteracja po plikach sesyjnych
		foreach(glob($this->_namespace . '*') as $sessionFile) {
			//usuwanie starych plików
			if (filemtime($sessionFile) < (time() - $maxLifetime)) {
				//usuwanie pliku
				unlink($sessionFile);
			}
		}
		return true;
	}
	
	/**
	 * Walidacja poprawności identyfikatora sesji
	 * @param string $id
	 * @return boolean
	 */
	private function _validate($id) {
		//litery i cyfry długości 8-128 znaków
		return preg_match('/^[a-z0-9]{8,128}$/', $id);
	}

}
