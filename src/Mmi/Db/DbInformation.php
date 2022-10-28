<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db;

use Mmi\Cache\SystemCacheInterface;

/**
 * Database information service interface
 */
class DbInformation implements DbInformationInterface
{
    public const CACHE_PREFIX = 'mmi-db-information-';

    /**
     * Contains table structure
     * @var array
     */
    private $tableStructure = [
        //default structure
        'mmi_cache' => [
            'id' => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'data' => ['dataType' => 'mediumtext', 'maxLength' => 16777215, 'null', 'default'],
            'ttl' => ['dataType' => 'int', 'maxLength', 'null' => 1, 'default'],
        ],
        'mmi_session' => [
            'id' => ['dataType' => 'varchar', 'maxLength' => 64, 'null', 'default'],
            'data' => ['dataType' => 'mediumtext', 'maxLength' => 16777215, 'null', 'default'],
            'timestamp' => ['dataType' => 'int', 'maxLength', 'null' => 1, 'default'],
        ],
    ];

    /**
     * @var DbInterface
     */
    private $db;

    /**
     * @var SystemCacheInterface
     */
    private $systemCache;

    /**
     * Constructor
     */
    public function __construct(DbInterface $db, SystemCacheInterface $systemCache)
    {
        //services
        $this->db           = $db;
        $this->systemCache  = $systemCache;
    }

    /**
     * Gets table structure as array (ie.):
     * [
     *  'fieldName' => ['dataType' => ??, 'maxLength' => ??, 'null' => 0|1, 'default' => ??],
     *  ...
     * ]
     */
    public function getTableStructure(string $tableName): array
    {
        //pobranie struktury z obiektu (wcześniej zapisane)
        if (isset($this->tableStructure[$tableName])) {
            return $this->tableStructure[$tableName];
        }
        //pobranie z cache
        $cacheKey = self::CACHE_PREFIX . $this->db->getConfig()->name . $tableName;
        if (null !== ($structure = $this->systemCache->load($cacheKey))) {
            return $structure;
        }
        //pobranie z adaptera
        $structure = $this->db->tableInfo($tableName);
        //zapis do bufora
        $this->systemCache->save($structure, $cacheKey, 0);
        //zwrot i zapis do tablicy zapisanych struktur
        return $this->tableStructure[$tableName] = $structure;
    }

    /**
     * Returns if given table contain field
     */
    public function isTableContainsField(string $tableName, string $fieldName): bool
    {
        return isset($this->getTableStructure($tableName)[$fieldName]);
    }

    /**
     * Resets any information to DB state
     */
    public function reset(): void
    {
        //usunięcie struktrur z cache
        foreach ($this->db->tableList() as $tableName) {
            $this->systemCache->remove(self::CACHE_PREFIX . $this->db->getConfig()->name . $tableName);
        }
        //usunięcie lokalnie zapisanych struktur
        $this->tableStructure = [];
    }
}
