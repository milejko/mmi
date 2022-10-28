<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Cache;

/**
 * Cache class implementing both System (private) and Cache (public)
 */
class Cache implements CacheInterface, SystemCacheInterface
{
    /**
     * Konfiguracja bufora
     * @var CacheConfig
     */
    private $_config;

    /**
     * Handler bufora
     * @var CacheHandlerInterface
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
    public const MAX_LIFETIME = 2592000;

    /**
     * Konstruktor, wczytuje konfigurację i ustawia handler
     */
    public function __construct(CacheConfig $config)
    {
        //ustawienie configu
        $this->_config = $config;
        //powoływanie rejestru
        $this->_registry = new CacheRegistry();
    }

    /**
     * Pobiera konfigurację
     * @return CacheConfig
     */
    public function getConfig(): CacheConfig
    {
        return $this->_config;
    }

    /**
     * Ładuje (jeśli istnieją) dane z bufora
     * @param string $key klucz
     * @return mixed
     * @throws CacheException
     */
    public function load(string $key)
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return null;
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
     * @throws CacheException
     */
    public function save($data, string $key, int $lifetime = null): bool
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
        return (bool)$this->_handler->save($key, $this->_setCacheData($data, time() + $lifetime), $lifetime);
    }

    /**
     * Usuwanie danych z bufora na podstawie klucza
     * @param string $key klucz
     * @return boolean
     * @throws CacheException
     */
    public function remove($key): bool
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return true;
        }
        //usunięcie z rejestru
        $this->getRegistry()->unsetOption($key);
        //usunięcie handlerem
        return (bool)$this->_handler->delete($key);
    }

    /**
     * Usuwa wszystkie dane z bufora
     */
    public function flush(): void
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return;
        }
        //czyszczenie rejestru
        $this->getRegistry()->setOptions([], true);
        //czyszczenie do handler
        $this->_handler->deleteAll();
    }

    /**
     * Zwraca aktywność cache
     * @return boolean
     * @throws CacheException
     */
    public function isActive(): bool
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
    protected function getRegistry()
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
     * @param CacheHandlerInterface $handler
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
    protected function validateAndPrepareBackendData($key, $data)
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
        //określanie klasy handlera
        $handlerClassName = '\\Mmi\\Cache\\' . ucfirst($this->_config->handler) . 'Handler';
        //powoływanie obiektu handlera
        $this->_setHandler(new $handlerClassName($this));
    }
}
