<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Orm;

use Mmi\Cache\Cache;
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
    protected $tableStructure = [
        'mmi_cache'     => [
            'id'   => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'data' => ['dataType' => 'mediumtext', 'maxLength' => 16777215, 'null', 'default'],
            'ttl'  => ['dataType' => 'int', 'maxLength', 'null' => 1, 'default'],
        ],
        'mmi_changelog' => [
            'filename' => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'md5'      => ['dataType' => 'varchar', 'maxLength' => 32, 'null', 'default'],
        ],
        'mmi_session'   => [
            'id'        => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'data'      => ['dataType' => 'mediumtext', 'maxLength' => 16777215, 'null', 'default'],
            'timestamp' => ['dataType' => 'int', 'maxLength', 'null' => 1, 'default'],
        ],
    ];
    
    /**
     * Adapter DB
     * @var PdoAbstract
     */
    protected $adapter;
    
    /**
     * Obiekt bufora
     * @var Cache
     */
    protected $cache;
    
    /**
     * DbConnector constructor.
     *
     * @param PdoAbstract $adapter
     * @param Cache            $cache
     */
    public function __construct(PdoAbstract $adapter, Cache $cache)
    {
        $this->adapter = $adapter;
        $this->cache   = $cache;
    }
    
    /**
     * Pobiera adapter bazodanowy
     * @return PdoAbstract
     */
    public function getAdapter()
    {
        return $this->adapter;
    }
    
    /**
     * Zwraca obiekt cache
     * @return Cache
     */
    public function getCache()
    {
        return $this->cache;
    }
    
    /**
     * Pobiera strukturę tabeli
     *
     * @param string $tableName opcjonalna nazwa tabeli
     *
     * @return array
     */
    public function getTableStructure($tableName)
    {
        //pobranie struktury z obiektu (wcześniej zapisane)
        if (isset($this->tableStructure[$tableName])) {
            return $this->tableStructure[$tableName];
        }
        //pobranie z cache
        $cacheKey = 'mmi-orm-structure-' . $this->getAdapter()->getConfig()->name . '-' . $tableName;
        if ($this->cache !== null && (null !== ($structure = $this->cache->load($cacheKey)))) {
            return $structure;
        }
        //pobranie z adaptera
        $structure = static::getAdapter()->tableInfo($tableName);
        //zapis do bufora
        if ($this->cache !== null) {
            $this->cache->save($structure, $cacheKey, 0);
        }
        
        //zwrot i zapis do tablicy zapisanych struktur
        return $this->tableStructure[$tableName] = $structure;
    }
    
    /**
     * Resetuje struktury tabel i usuwa cache
     * @return boolean
     */
    public function resetTableStructures()
    {
        //usunięcie struktrur z cache
        foreach ($this->getAdapter()->tableList() as $tableName) {
            $this->cache->remove('mmi-orm-structure-' . $this->getAdapter()->getConfig()->name . '-' . $tableName);
        }
        //usunięcie lokalnie zapisanych struktur
        $this->tableStructure = [];
        
        return true;
    }
    
    /**
     * Zwraca obecność pola w tabeli
     *
     * @param string $fieldName nazwa pola
     * @param string $tableName opcjonalna nazwa tabeli
     *
     * @return boolean
     */
    public function fieldInTable($fieldName, $tableName)
    {
        return isset($this->getTableStructure($tableName)[$fieldName]);
    }
    
    /**
     * Zwraca nazwę rekordu dla podanej tabeli
     *
     * @param string $tableName
     *
     * @return string
     */
    public function getRecordNameByTable($tableName)
    {
        //rozdzielenie po podkreślniku
        $tableArray   = explode('_', $tableName);
        $namespace    = ucfirst($tableArray[0]) . '\\Orm\\';
        $tableArray[] = 'Record';
        //dołączenie pozostałych parametrów
        foreach ($tableArray as $key => $element) {
            $tableArray[$key] = ucfirst($element);
        }
        
        //łączenie z namespace
        return $namespace . implode('', $tableArray);
    }
    
}
