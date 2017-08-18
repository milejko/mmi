<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Session;

/**
 * Klasa obsługi sesji w apc
 */
class ApcHandler implements \SessionHandlerInterface
{

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
    public function open($savePath, $sessionName)
    {
        $this->_namespace = 'sess-' . crc32($sessionName . $savePath) . '-';
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
        //pobieranie z apcu
        if (!($data = \apcu_fetch($this->_namespace . $id))) {
            //nie może zwracać null
            return ($this->_data = '');
        }
        //zwrot danych
        return ($this->_data = $data);
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
        //usuwanie danych
        \apcu_delete($this->_namespace . $id);
        return true;
    }

    /**
     * Garbage collector
     * @param integer $maxLifetime
     * @return boolean
     */
    public function gc($maxLifetime)
    {
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
