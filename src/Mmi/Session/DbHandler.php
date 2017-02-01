<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Session;

use \Mmi\Orm,
	Mmi\App\FrontController;

/**
 * Klasa obsługi sesji w bazie danych
 */
class DbHandler implements \SessionHandlerInterface {

	/**
	 * Prefiks pustych sesji w buforze
	 */
	CONST CACHE_PREFIX = 'nosess-';

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
		//zapisano w cache informację o braku danych w tej sesji
		if (true === $this->_isSessionEmpty($id)) {
			return '';
		}
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
			//zapis informacji do cache o braku danych w tej sesji
			return $this->_noticeEmptySession($id);
		}
		//ustawianie danych i czasu
		$record->id = $id;
		$record->data = $data;
		$record->timestamp = time();
		//usuwanie informacji o pustej sesji i zapis rekordu
		return $record->save() && $this->_cleanEmptySession($id);
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
		if (null === $record = (new Orm\SessionQuery)->findPk($id)) {
			return $this->_cleanEmptySession($id);
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
	 * Ustawienie w buforze pustej sesji
	 * @param string $id
	 * @return boolean
	 */
	private function _noticeEmptySession($id) {
		return FrontController::getInstance()->getLocalCache()->save(true, self::CACHE_PREFIX . $id);
	}

	/**
	 * Czy sesja jest pusta (informacja z bufora)
	 * @param string $id
	 * @return boolean
	 */
	private function _isSessionEmpty($id) {
		return FrontController::getInstance()->getLocalCache()->load(self::CACHE_PREFIX . $id);
	}

	/**
	 * Czyszczenie informacji o pustości sesji
	 * @param string $id
	 * @return boolean
	 */
	private function _cleanEmptySession($id) {
		return FrontController::getInstance()->getLocalCache()->remove(self::CACHE_PREFIX . $id);
	}

}
