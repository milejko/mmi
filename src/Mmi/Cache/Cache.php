<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Cache;

/**
 * Obiekt bufora danych
 */
class Cache
{

    /**
     * Konfiguracja bufora
     * @var CacheConfig
     */
    private $_config;

    /**
     * Handler bufora
     * @var HandlerInterface
     */
    private $_handler;

    /**
     * Rejestr bufora
     * @var CacheRegistry
     */
    private $_registry;

    /**
     * Maksymalna długość bufora
     */
    CONST MAX_LIFETIME = 2592000;

    /**
     * Konstruktor, wczytuje konfigurację i ustawia handler
     */
    public function __construct(CacheConfig $config)
    {
        //ustawienie configu
        $this->_config = $config;
        //powoływanie rejestru
        $this->_registry = new CacheRegistry;
    }

    /**
     * Pobiera konfigurację
     * @return CacheConfig
     */
    public function getConfig()
    {
        return $this->_config;
    }

    /**
     * Ładuje (jeśli istnieją) dane z bufora
     * @param string $key klucz
     * @return mixed
     */
    public function load($key)
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return;
        }
        //pobranie z rejestru aplikacji jeśli istnieje
        if ($this->getRegistry()->issetOption($key)) {
            return $this->getRegistry()->getOption($key);
        }
        //zwrot zwalidowanych danych
        return $this->validateAndPrepareBackendData($key, $this->_handler->load($key));
    }

    /**
     * Zapis danych
     * Dane zostaną zserializowane i zapisane w handlerzie
     * @param mixed $data dane
     * @param string $key klucz
     * @param integer $lifetime czas życia
     * @return boolean
     */
    public function save($data, $key, $lifetime = null)
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return true;
        }
        //brak podanego klucza (użycie domyślnego z cache)
        if (!$lifetime) {
            //jeśli null - użycie domyślnego, jeśli zero lub false to maksymalny
            $lifetime = $lifetime === null ? $this->_config->lifetime : ($lifetime == 0 ? self::MAX_LIFETIME : $lifetime);
        }
        //zapis w rejestrze
        $this->getRegistry()->setOption($key, $data);
        //zapis w handlerzie
        return (bool) $this->_handler->save($key, $this->_setCacheData($data, time() + $lifetime), $lifetime);
    }

    /**
     * Usuwanie danych z bufora na podstawie klucza
     * @param string $key klucz
     * @return boolean
     */
    public function remove($key)
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return true;
        }
        //usunięcie z rejestru
        $this->getRegistry()->unsetOption($key);
        //usunięcie handlerem
        return (bool) $this->_handler->delete($key);
    }

    /**
     * Usuwa wszystkie dane z bufora
     */
    public function flush()
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return;
        }
        //czyszczenie rejestru
        $this->getRegistry()->setOptions([]);
        //czyszczenie do handler
        $this->_handler->deleteAll();
    }

    /**
     * Zwraca aktywność cache
     * @return boolean
     */
    public function isActive()
    {
        //sprawdzenie aktywności
        if (!$this->_config->active) {
            return false;
        }
        //ustawienie handleru
        $this->_setupHandler();
        return true;
    }

    /**
     * Zwraca rejestr bufora
     * @return CacheRegistry
     */
    public function getRegistry()
    {
        return $this->_registry;
    }

    /**
     * Serializuje dane i stempluje datą wygaśnięcia
     * @param mixed $data dane
     * @param int $expire wygasa
     * @return string
     */
    protected function _setCacheData($data, $expire)
    {
        return \serialize(['d' => $data, 'e' => $expire]);
    }

    /**
     * Ustawia handler bufora
     * @param \Mmi\Cache\CacheHandlerInterface $handler
     */
    protected function _setHandler(CacheHandlerInterface $handler)
    {
        $this->_handler = $handler;
    }

    /**
     * Zwraca aktualne dane (sprawdza ważność)
     * @param mixed $data dane
     * @return mixed
     */
    public function validateAndPrepareBackendData($key, $data)
    {
        //niepoprawna serializacja
        if (!($data = \unserialize($data))) {
            return;
        }
        //dane niepoprawne
        if (!array_key_exists('e', $data) || !array_key_exists('d', $data)) {
            return;
        }
        //dane wygasłe
        if ($data['e'] <= time()) {
            return;
        }
        //zapis danych do rejestru
        $this->getRegistry()->setOption($key, $data['d']);
        //zwrot danych
        return $data['d'];
    }

    /**
     * Ustawianie handlera
     * @throws CacheException
     */
    protected function _setupHandler()
    {
        //handler już ustawiony
        if (null !== $this->_handler) {
            return;
        }
        //próba powołania handlera
        try {
            //określanie klasy handlera
            $handlerClassName = '\\Mmi\\Cache\\' . ucfirst($this->_config->handler) . 'Handler';
            //powoływanie obiektu handlera
            $this->_setHandler(new $handlerClassName($this));
        } catch (\Exception $e) {
            //błąd powołania handlera
            throw new CacheException('Unable to initialize handler: ' . $e->getMessage());
        }
    }

}
