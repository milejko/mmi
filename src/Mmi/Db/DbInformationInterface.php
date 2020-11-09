<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Db;

/**
 * Database information service interface
 */
interface DbInformationInterface
{
    /**
     * Gets table structure as array (ie.):
     * [
     *  'fieldName' => ['dataType' => ??, 'maxLength' => ??, 'null' => 0|1, 'default' => ??],
     *  ...
     * ]
     */
    public function getTableStructure(string $tableName): array;

    /**
     * Returns if given table contain field
     */
    public function isTableContainsField(string $tableName, string $fieldName): bool;

    /**
     * Resets any information to DB state
     */
    public function reset(): void;

}