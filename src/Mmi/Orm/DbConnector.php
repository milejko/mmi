<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

use Mmi\App\App;
use Mmi\Cache\Cache;
use Mmi\Cache\PrivateCache;
use Mmi\Db\Adapter\PdoAbstract;

/**
 * Klasa dostępu do tabel w bazie danych
 */
class DbConnector
{

    /**
     * Przechowuje strukturę bazy danych
     * @var array
     */
    protected static $_tableStructure = [
        'mmi_cache' => [
            'id' => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'data' => ['dataType' => 'mediumtext', 'maxLength' => 16777215, 'null', 'default'],
            'ttl' => ['dataType' => 'int', 'maxLength', 'null' => 1, 'default'],
        ],
        'mmi_changelog' => [
            'filename' => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'md5' => ['dataType' => 'varchar', 'maxLength' => 32, 'null', 'default'],
        ],
        'mmi_session' => [
            'id' => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'data' => ['dataType' => 'mediumtext', 'maxLength' => 16777215, 'null', 'default'],
            'timestamp' => ['dataType' => 'int', 'maxLength', 'null' => 1, 'default'],
        ],
    ];

    /**
     * Pobiera adapter bazodanowy
     * @return \Mmi\Db\Adapter\PdoAbstract
     */
    public static final function getAdapter()
    {
        return App::$di->get(PdoAbstract::class);
    }

    /**
     * Zwraca obiekt cache
     * @return \Mmi\Cache\Cache
     */
    public static final function getCache()
    {
        return App::$di->get(PrivateCache::class);
    }

    /**
     * Pobiera strukturę tabeli
     * @param string $tableName opcjonalna nazwa tabeli
     * @return array
     */
    public static final function getTableStructure($tableName)
    {
        //pobranie struktury z obiektu (wcześniej zapisane)
        if (isset(self::$_tableStructure[$tableName])) {
            return self::$_tableStructure[$tableName];
        }
        //pobranie z cache
        $cacheKey = 'mmi-orm-structure-' . self::getAdapter()->getConfig()->name . '-' . $tableName;
        if (self::getCache()->isActive() && (null !== ($structure = self::getCache()->load($cacheKey)))) {
            return $structure;
        }
        //pobranie z adaptera
        $structure = static::getAdapter()->tableInfo($tableName);
        //zapis do bufora
        if (self::getCache()->isActive()) {
            self::getCache()->save($structure, $cacheKey, 0);
        }
        //zwrot i zapis do tablicy zapisanych struktur
        return self::$_tableStructure[$tableName] = $structure;
    }

    /**
     * Resetuje struktury tabel i usuwa cache
     * @return boolean
     */
    public static final function resetTableStructures()
    {
        //usunięcie struktrur z cache
        foreach (self::getAdapter()->tableList() as $tableName) {
            self::getCache()->isActive() && self::getCache()->remove('mmi-orm-structure-' . self::getAdapter()->getConfig()->name . '-' . $tableName);
        }
        //usunięcie lokalnie zapisanych struktur
        self::$_tableStructure = [];
        return true;
    }

    /**
     * Zwraca obecność pola w tabeli
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli
     * @return boolean
     */
    public static final function fieldInTable($fieldName, $tableName)
    {
        return isset(self::getTableStructure($tableName)[$fieldName]);
    }

    /**
     * Zwraca nazwę rekordu dla podanej tabeli
     * @param string $tableName
     * @return string
     */
    public static final function getRecordNameByTable($tableName)
    {
        //rozdzielenie po podkreślniku
        $tableArray = explode('_', $tableName);
        $namespace = ucfirst($tableArray[0]) . '\\Orm\\';
        $tableArray[] = 'Record';
        //dołączenie pozostałych parametrów
        foreach ($tableArray as $key => $element) {
            $tableArray[$key] = ucfirst($element);
        }
        //łączenie z namespace
        return $namespace . implode('', $tableArray);
    }

}
