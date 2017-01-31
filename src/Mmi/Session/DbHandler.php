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
	 * Zamknięcie sesji (nie robi nic)
	 * @return boolean
	 */
	public function close() {
		return true;
	}

	/**
	 * Usunięcie sesji
	 * @param string $session_id
	 * @return boolean
	 */
	public function destroy($session_id) {
		//brak rekordu
		if (null === $record = (new Orm\SessionQuery)->findPk($session_id)) {
			return true;
		}
		//usuwanie rekordu
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

	/**
	 * Otwarcie sesji (nie robi nic)
	 * @param string $save_path
	 * @param string $session_name
	 * @return boolean
	 */
	public function open($save_path, $session_name) {
		return true;
	}

	/**
	 * Odczyt danych do sesji
	 * @param string $session_id
	 * @return mixed
	 */
	public function read($session_id) {
		//wyszukiwanie rekordu
		if (null === $record = (new Orm\SessionQuery)->findPk($session_id)) {
			//nie może zwracać null
			return '';
		}
		return $record->data;
	}

	/**
	 * Zapis danych w sesji
	 * @param string $session_id
	 * @param mixed $data
	 * @return boolean
	 */
	public function write($session_id, $data) {
		//wyszukiwanie rekordu
		if (null === $record = (new Orm\SessionQuery)->findPk($session_id)) {
			//tworzenie nowego rekordu
			$record = new Orm\SessionRecord;
			$record->id = $session_id;
		}
		//brak danych i brak zapisanej sesji - nie zapisujemy w bazie
		if (!$data && !$record->id) {
			return true;
		}
		//brak danych i istnieje rekord - usuwanie
		if (!$data && $record->id) {
			$record->delete();
			return true;
		}
		//ustawianie danych i czasu
		$record->data = $data;
		$record->timestamp = time();
		//zapis rekordu
		return $record->save();
	}

}
