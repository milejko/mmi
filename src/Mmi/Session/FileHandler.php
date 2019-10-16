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
 * Klasa oszczędnej obsługi sesji w plikach
 */
class FileHandler implements \SessionHandlerInterface
{

    /**
     * Namespace sesji
     */
    private $_namespace = BASE_PATH . '/var/session/sess-';

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
        if ($savePath) {
            $this->_namespace = $savePath . '/';
        }
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
            //próba czyszczenie jeśli plik znaleziony
            try {
                unlink($this->_namespace . $id);
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
        //próba usunięcia danych
        try {
            unlink($this->_namespace . $id);
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
    public function gc($maxLifetime)
    {
        //iteracja po plikach sesyjnych
        foreach (glob($this->_namespace . '*') as $sessionFile) {
            //próba określenia daty pliku
            try {
                $fileTime = filemtime($sessionFile);
            } catch (\Exception $e) {
                //plik już skasowany
                continue;
            }
            //sprawdzenie czy plik jest stary
            if ($fileTime > (time() - $maxLifetime)) {
                //nie jest
                continue;
            }
            //próba usunięcia pliku
            try {
                unlink($sessionFile);
            } catch (\Exception $e) {
                //nie udało się usunąć (usunięty przez inny proces)
            }
        }
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
