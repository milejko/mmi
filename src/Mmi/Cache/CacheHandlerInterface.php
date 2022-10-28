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
 * Interfejs handlerów bufora
 */
interface CacheHandlerInterface
{
    /**
     * Konstruktor
     * @param Cache $cache obiekt bufora
     */
    public function __construct(Cache $cache);

    /**
     * Ładuje dane o podanym kluczu
     * @param string $key klucz
     */
    public function load($key);

    /**
     * Zapisuje dane pod podanym kluczem
     * @param string $key klucz
     * @param string $data
     * @param int $lifeTime wygaśnięcie danych w buforze (informacja dla bufora)
     */
    public function save($key, $data, $lifeTime);

    /**
     * Kasuje dane o podanym kluczu
     * @param string $key klucz
     */
    public function delete($key);

    /**
     * Kasuje wszystkie dane
     */
    public function deleteAll();
}
