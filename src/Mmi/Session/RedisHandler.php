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
 * Klasa oszczędnej obsługi sesji w redis
 */
class RedisHandler implements \SessionHandlerInterface
{

    /**
     * Przechowuje obiekt Redisa
     * @var \Redis
     */
    private $_server;

    /**
     * Dane w sesji
     * @var mixed
     */
    private $_data;

    /**
     * Otwarcie sesji
     * @param string $savePath
     * @param string $sessionName
     * @throws SessionException
     * @return boolean
     */
    public function open($savePath, $sessionName)
    {
        //powoływanie serwera
        $this->_server = new \Redis;
        //parsowanie konfiguracji
        $config = parse_url($savePath);
        //format połączenie host/port
        if (!isset($config['host']) || !isset($config['port'])) {
            //błąd konfiguracji
            throw new SessionException('Configuration path invalid');
        }
        //łączenie host/port
        $this->_server->pconnect($config['host'], $config['port']);
        //autoryzacja
        if (isset($config['user'])) {
            $this->_server->auth($config['user'] . (isset($config['pass']) ? ':' . $config['pass'] : ''));
        }
        //wybór bazy
        $this->_server->select((isset($config['path']) ? ltrim($config['path'], '/') : '1'));
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
        if (!($data = $this->_server->get($id))) {
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
        //puste dane sesyjne
        if (!$data) {
            //usuwamy
            $this->_server->del($id);
            return true;
        }
        //ustawianie danych w Redis
        $this->_server->set($id, $data);
        //zapis rekordu
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
        //zapis pustej sesji
        $this->_server->del($id);
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
