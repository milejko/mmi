<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Session;

use \Mmi\Orm;

/**
 * Klasa obsługi sesji w bazie danych
 */
class DbHandler implements \SessionHandlerInterface {

	/**
	 * Otwarcie sesji
	 * @param string $savePath
	 * @param string $sessionName
	 * @return boolean
	 */
	public function open($savePath, $sessionName) {
		return true;
	}

	/**
	 * Odczyt danych do sesji
	 * @param string $id
	 * @return mixed
	 */
	public function read($id) {
		//wyszukiwanie rekordu
		if (null === $record = (new Orm\SessionQuery)->findPk($id)) {
			//nie może zwracać null
			return '';
		}
		return $record->data;
	}

	/**
	 * Zapis danych w sesji
	 * @param string $id
	 * @param mixed $data
	 * @return boolean
	 */
	public function write($id, $data) {
		//wyszukiwanie rekordu
		if (null === $record = (new Orm\SessionQuery)->findPk($id)) {
			//tworzenie nowego rekordu
			$record = new Orm\SessionRecord;
		}
		//brak danych
		if (!$data) {
			//jeśli istniał rekord - usuwamy
			$record->id ? $record->delete() : null;
		}
		//ustawianie danych i czasu
		$record->id = $id;
		$record->data = $data;
		$record->timestamp = time();
		//zapis rekordu
		return $record->save();
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
		//brak rekordu
		if ((null === $record = (new Orm\SessionQuery)->findPk($id))) {
			return true;
		}
		$record->delete();
		return true;
	}

	/**
	 * Garbage collector
	 * @param integer $maxLifetime
	 * @return boolean
	 */
	public function gc($maxLifetime) {
		//uproszczone usuwanie - jednym zapytaniem
		\Mmi\Orm\DbConnector::getAdapter()->delete((new Orm\SessionQuery)->getTableName(), 'WHERE timestamp < :time', [':time' => (time() - $maxLifetime)]);
		return true;
	}

}
