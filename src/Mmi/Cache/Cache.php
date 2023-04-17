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
     * Handler bufora
     * @var CacheHandlerInterface
     */
    private $handler;

    /**
     * Rejestr bufora
     */
    private array $registry = [];

    /**
     * Maksymalna długość bufora
     */
    public const MAX_LIFETIME = 2592000;

    /**
     * Konstruktor, wczytuje konfigurację i ustawia handler
     */
    public function __construct(private CacheConfig $config)
    {
    }

    /**
     * Pobiera konfigurację
     * @return CacheConfig
     */
    public function getConfig(): CacheConfig
    {
        return $this->config;
    }

    /**
     * Ładuje (jeśli istnieją) dane z bufora
     * @param string $key klucz
     * @return mixed
     * @throws CacheException
     */
    public function load(string $key): mixed
    {
        //bufor nieaktywny
        if (!$this->isActive()) {
            return null;
        }
        //pobranie z rejestru jeśli istnieje
        if (array_key_exists($key, $this->registry)) {
            return $this->registry[$key];
        }
        //zwrot zwalidowanych danych
        return $this->validateAndPrepareBackendData($key, $this->handler->load($key));
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
            $lifetime = $lifetime === null ? $this->config->lifetime : ($lifetime == 0 ? self::MAX_LIFETIME : $lifetime);
        }
        //zapis w rejestrze
        $this->registry[$key] = $data;
        //zapis w handlerzie
        return (bool)$this->handler->save($key, $this->setCacheData($data, time() + $lifetime), $lifetime);
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
        unset($this->registry[$key]);
        //usunięcie handlerem
        return (bool)$this->handler->delete($key);
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
        $this->registry = [];
        //czyszczenie w handlerze
        $this->handler->deleteAll();
    }

    /**
     * Zwraca aktywność cache
     * @return boolean
     * @throws CacheException
     */
    public function isActive(): bool
    {
        //sprawdzenie aktywności
        if (!$this->config->active) {
            return false;
        }
        //ustawienie handleru
        $this->setupHandler();
        return true;
    }

    /**
     * Serializuje dane i stempluje datą wygaśnięcia
     */
    protected function setCacheData(mixed $data, int $expire): string
    {
        return \serialize(['d' => $data, 'e' => $expire]);
    }

    /**
     * Ustawia handler bufora
     * @param CacheHandlerInterface $handler
     */
    protected function setHandler(CacheHandlerInterface $handler): void
    {
        $this->handler = $handler;
    }

    /**
     * Zwraca aktualne dane (sprawdza ważność)
     * @param mixed $data dane
     * @return mixed
     */
    protected function validateAndPrepareBackendData($key, $data): mixed
    {
        //brak danych
        if (null === $data) {
            return null;
        }
        //niepoprawna serializacja
        if (!($data = \unserialize($data))) {
            return null;
        }
        //dane niepoprawne
        if (!array_key_exists('e', $data) || !array_key_exists('d', $data)) {
            return null;
        }
        //dane wygasłe
        if ($data['e'] <= time()) {
            return null;
        }
        //zapis danych do rejestru
        $this->registry[$key] = $data['d'];
        //zwrot danych
        return $data['d'];
    }

    /**
     * Ustawianie handlera
     * @throws CacheException
     */
    protected function setupHandler(): void
    {
        //handler już ustawiony
        if (null !== $this->handler) {
            return;
        }
        //określanie klasy handlera
        $handlerClassName = '\\Mmi\\Cache\\' . ucfirst($this->config->handler) . 'Handler';
        //powoływanie obiektu handlera
        $this->setHandler(new $handlerClassName($this));
    }
}
