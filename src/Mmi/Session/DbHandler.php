<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

use \Mmi\Orm;

/**
 * Klasa obsługi sesji w bazie danych
 */
class DbHandler implements \SessionHandlerInterface
{

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
    public function open($savePath, $sessionName)
    {
        return true;
    }

    /**
     * Odczyt danych do sesji
     * @param string $id
     * @return mixed
     */
    public function read($id)
    {
        //niepoprawne ID
        if (!$this->_validate($id)) {
            return '';
        }
        //wyszukiwanie rekordu
        if (null === $record = (new Orm\SessionQuery)->findPk($id)) {
            //nie może zwracać null
            return ($this->_data = '');
        }
        //zwrot danych
        return ($this->_data = $record->data);
    }

    /**
     * Zapis danych w sesji
     * @param string $id
     * @param mixed $data
     * @return boolean
     */
    public function write($id, $data)
    {
        //dane nie uległy zmianie
        if ($data == $this->_data) {
            return true;
        }
        //niepoprawne ID
        if (!$this->_validate($id)) {
            return true;
        }
        //wyszukiwanie rekordu
        if (null === $record = (new Orm\SessionQuery)->findPk($id)) {
            //tworzenie nowego rekordu
            $record = new Orm\SessionRecord;
        }
        //puste dane sesyjne
        if (!$data) {
            //jeśli istniał rekord - usuwamy
            $record->id ? $record->delete() : null;
            return true;
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
    public function close()
    {
        return true;
    }

    /**
     * Usunięcie sesji
     * @param string $id
     * @return boolean
     */
    public function destroy($id)
    {
        //niepoprawne ID
        if (!$this->_validate($id)) {
            return true;
        }
        //brak rekordu
        if ((null === $record = (new Orm\SessionQuery)->findPk($id))) {
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
    public function gc($maxLifetime)
    {
        //uproszczone usuwanie - jednym zapytaniem
        \Mmi\Orm\DbConnector::getAdapter()->delete((new Orm\SessionQuery)->getTableName(), 'WHERE timestamp <= :time', [':time' => (time() - $maxLifetime)]);
        return true;
    }

    /**
     * Walidacja poprawności identyfikatora sesji
     * @param string $id
     * @return boolean
     */
    private function _validate($id)
    {
        //litery i cyfry długości 8-128 znaków
        return preg_match('/^[a-z0-9]{8,128}$/', $id);
    }

}
