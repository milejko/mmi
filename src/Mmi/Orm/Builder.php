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
use Mmi\Db\DbInformationInterface;

/**
 * Klasa budowniczego struktur DAO/Record/Query
 */
class Builder
{

    CONST INDENT = "    ";
    
    /**
     * Renderuje DAO, Record i Query dla podanej nazwy tabeli
     * @param string $tableName
     * @throws \Mmi\Orm\OrmException
     */
    public static function buildFromTableName($tableName)
    {
        //pomijanie modułów z vendorów
        foreach (\Mmi\Mvc\StructureParser::getModules() as $module) {
            if (strtolower(basename($module)) == explode('_', $tableName)[0] && false !== strpos($module, 'vendor')) {
                return;
            }
        }
        //aktualizacja QUERY-FIELD
        self::_updateQueryField($tableName);
        //aktualizacja QUERY-JOIN
        self::_updateQueryJoin($tableName);
        //aktualizacja QUERY
        self::_updateQuery($tableName);
        //aktualizacja RECORD
        self::_updateRecord($tableName);
    }

    /**
     * Tworzy, lub aktualizuje rekord
     * @param string $tableName
     */
    protected static function _updateRecord($tableName)
    {
        //kod rekordu
        $recordCode = '<?php' . "\n\n" .
            'namespace ' . self::_getNamespace($tableName) . ";\n\n" .
            'class ' . ($className = self::_getNamePrefix($tableName) . 'Record') . ' extends \Mmi\Orm\Record' .
            "\n{" .
            "\n\n" .
            '}' . "\n";
        //ścieżka do pliku
        $path = self::_mkdirRecursive(self::_getPathPrefix($tableName)) . '/' . $className . '.php';
        //wczytanie istniejącego rekordu
        if (file_exists($path)) {
            $recordCode = file_get_contents($path);
        }
        //odczyt struktury tabeli
        $structure = App::$di->get(DbInformationInterface::class)->getTableStructure($tableName);
        //błędna struktrura lub brak
        if (empty($structure)) {
            throw new OrmException('\Mmi\Orm\Builder: no table found, or table invalid: ' . $tableName);
        }
        $variableString = "\n";
        //generowanie pól rekordu
        foreach ($structure as $fieldName => $fieldDetails) {
            $variables[] = Convert::underscoreToCamelcase($fieldName);
            $variableString .= self::INDENT . 'public $' . Convert::underscoreToCamelcase($fieldName) . ";\n";
        }
        //sprawdzanie istnienia pól rekordu
        if (preg_match_all('/' . self::INDENT . 'public \$([a-zA-Z0-9\_]+)[\;|\s\=]/', $recordCode, $codeVariables) && isset($codeVariables[1])) {
            //za dużo względem bazy
            $diffRecord = array_diff($codeVariables[1], $variables);
            //brakujące względem DB
            $diffDb = array_diff($variables, $codeVariables[1]);
            //pola się nie zgadzają
            if (!empty($diffRecord) || !empty($diffDb)) {
                throw new OrmException('RECORD for: "' . $tableName . '" has invalid fields: ' . implode(', ', $diffRecord) . ', and missing: ' . implode(',', $diffDb));
            }
            return;
        }
        $recordCode = preg_replace('/(class ' . $className . ' extends [\\a-zA-Z0-9]+\n\{?\r?\n?)/', '$1' . $variableString, $recordCode);
        //zapis pliku
        file_put_contents($path, $recordCode);
    }

    /**
     * Tworzy lub aktualizuje pole query
     * @param string $tableName
     */
    protected static function _updateQueryField($tableName)
    {
        //prefixy nazw
        $queryClassName = self::_getNamespace($tableName) . '\\' . self::_getNamePrefix($tableName) . 'Query';
        //odczyt struktury
        $structure = App::$di->get(DbInformationInterface::class)->getTableStructure($tableName);
        $methods = '';
        //budowanie komentarzy do metod
        foreach ($structure as $fieldName => $fieldDetails) {
            $fieldName = ucfirst(Convert::underscoreToCamelcase($fieldName));
            //metody equalsColumn... np. equalsColumnActive()
            $methods .= ' * @method \\' . $queryClassName . ' equalsColumn' . $fieldName . '()' . "\n";
            //notEqualsColumn
            $methods .= ' * @method \\' . $queryClassName . ' notEqualsColumn' . $fieldName . '()' . "\n";
            //greaterThanColumn
            $methods .= ' * @method \\' . $queryClassName . ' greaterThanColumn' . $fieldName . '()' . "\n";
            //lessThanColumn
            $methods .= ' * @method \\' . $queryClassName . ' lessThanColumn' . $fieldName . '()' . "\n";
            //greaterOrEqualsColumn
            $methods .= ' * @method \\' . $queryClassName . ' greaterOrEqualsColumn' . $fieldName . '()' . "\n";
            //lessOrEqualsColumn
            $methods .= ' * @method \\' . $queryClassName . ' lessOrEqualsColumn' . $fieldName . '()' . "\n";
        }
        //anotacje dla metod porównujących (equals itp.)
        $queryCode = '<?php' . "\n\n" .
            'namespace ' . self::_getNamespace($tableName) . '\QueryHelper' . ";\n\n" .
            '/**' . "\n" .
            ' * @method \\' . $queryClassName . ' equals($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' notEquals($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' greater($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' less($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' greaterOrEquals($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' lessOrEquals($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' like($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' notLike($value)' . "\n" .
            ' * @method \\' . $queryClassName . ' between($from, $to)' . "\n" .
            $methods .
            ' */' . "\n" .
            'class ' . ($className = self::_getNamePrefix($tableName) . 'QueryField') . ' extends \Mmi\Orm\QueryHelper\QueryField' .
            "\n{" .
            "\n\n" .
            '}' . "\n";
        //zapis pliku
        file_put_contents(self::_mkdirRecursive(self::_getPathPrefix($tableName) . '/QueryHelper') . '/' . $className . '.php', $queryCode);
    }

    /**
     * Tworzy lub aktualizuje obiekt złączenia (JOIN)
     * @param string $tableName
     */
    protected static function _updateQueryJoin($tableName)
    {
        //prefixy nazw
        $queryClassName = self::_getNamespace($tableName) . '\\' . self::_getNamePrefix($tableName) . 'Query';
        //anotacja dla metody on()
        $queryCode = '<?php' . "\n\n" .
            'namespace ' . self::_getNamespace($tableName) . '\QueryHelper' . ";\n\n" .
            '/**' . "\n" .
            ' * @method \\' . $queryClassName . ' on($localKeyName, $joinedKeyName = \'id\')' . "\n" .
            ' */' . "\n" .
            'class ' . ($className = self::_getNamePrefix($tableName) . 'QueryJoin') . ' extends \Mmi\Orm\QueryHelper\QueryJoin' .
            "\n{" .
            "\n\n" .
            '}' . "\n";
        //zapis pliku
        file_put_contents(self::_mkdirRecursive(self::_getPathPrefix($tableName) . '/QueryHelper') . '/' . $className . '.php', $queryCode);
    }

    /**
     * Tworzy, lub aktualizuje zapytanie
     * @param string $tableName
     */
    protected static function _updateQuery($tableName)
    {
        //prefixy nazw
        $namePrefix = self::_getNamePrefix($tableName);
        //nazwa klasy
        $className = $namePrefix . 'Query';
        //nazwa klasy pola
        $fieldClassName = $namePrefix . 'QueryField';
        //nazwa klasy złączenia
        $joinClassName = $namePrefix . 'QueryJoin';
        //nazwa rekordu
        $recordClassName = $namePrefix . 'Record';
        //ścieżka
        $path = self::_mkdirRecursive(self::_getPathPrefix($tableName)) . '/' . $className . '.php';
        //kod zapytania
        $queryCode = "{\n\n" . self::INDENT .
            'protected $_tableName = \'' .
            $tableName . '\';' .
            "\n\n}";
        //wczytanie istniejącego rekordu
        if (file_exists($path)) {
            $queryCode = file_get_contents($path);
        }
        //odczyt struktury
        $structure = App::$di->get(DbInformationInterface::class)->getTableStructure($tableName);
        //pusta, lub błędna struktura
        if (empty($structure)) {
            throw new OrmException('\Mmi\Orm\Builder: no table found, or table invalid: ' . $tableName);
        }

        $methods = '';
        //budowanie komentarzy do metod
        foreach ($structure as $fieldName => $fieldDetails) {
            $fieldName = ucfirst(Convert::underscoreToCamelcase($fieldName));
            //metody where... np. whereActive()
            $methods .= ' * @method QueryHelper\\' . $fieldClassName . ' where' . $fieldName . '()' . "\n";
            //metody andField... np. andFieldActive()
            $methods .= ' * @method QueryHelper\\' . $fieldClassName . ' andField' . $fieldName . '()' . "\n";
            //orField
            $methods .= ' * @method QueryHelper\\' . $fieldClassName . ' orField' . $fieldName . '()' . "\n";
            //orderAsc
            $methods .= ' * @method ' . $className . ' orderAsc' . $fieldName . '()' . "\n";
            //orderDesc
            $methods .= ' * @method ' . $className . ' orderDesc' . $fieldName . '()' . "\n";
            //groupBy
            $methods .= ' * @method ' . $className . ' groupBy' . $fieldName . '()' . "\n";
        }
        $queryHead = '<?php' . "\n\n" .
            'namespace ' . self::_getNamespace($tableName) . ";\n\n" .
            '//<editor-fold defaultstate="collapsed" desc="' . $className . '">' . "\n" .
            '/**' . "\n" .
            ' * @method ' . $className . ' limit($limit = null)' . "\n" .
            ' * @method ' . $className . ' offset($offset = null)' . "\n" .
            ' * @method ' . $className . ' orderAsc($fieldName, $tableName = null)' . "\n" .
            ' * @method ' . $className . ' orderDesc($fieldName, $tableName = null)' . "\n" .
            ' * @method ' . $className . ' andQuery(\Mmi\Orm\Query $query)' . "\n" .
            ' * @method ' . $className . ' whereQuery(\Mmi\Orm\Query $query)' . "\n" .
            ' * @method ' . $className . ' orQuery(\Mmi\Orm\Query $query)' . "\n" .
            ' * @method ' . $className . ' resetOrder()' . "\n" .
            ' * @method ' . $className . ' resetWhere()' . "\n" .
            $methods .
            ' * @method QueryHelper\\' . $fieldClassName . ' andField($fieldName, $tableName = null)' . "\n" .
            ' * @method QueryHelper\\' . $fieldClassName . ' where($fieldName, $tableName = null)' . "\n" .
            ' * @method QueryHelper\\' . $fieldClassName . ' orField($fieldName, $tableName = null)' . "\n" .
            ' * @method QueryHelper\\' . $joinClassName . ' join($tableName, $targetTableName = null, $alias = null)' . "\n" .
            ' * @method QueryHelper\\' . $joinClassName . ' joinLeft($tableName, $targetTableName = null, $alias = null)' . "\n" .
            ' * @method ' . $recordClassName . '[] find()' . "\n" .
            ' * @method ' . $recordClassName . ' findFirst()' . "\n" .
            ' * @method ' . $recordClassName . ' findPk($value)' . "\n" .
            ' */' . "\n" . '//</editor-fold>' . "\n" . 'class ' . $className . ' extends \Mmi\Orm\Query';
        $queryCode = $queryHead . "\n" . substr($queryCode, strpos($queryCode, '{'));
        file_put_contents($path, $queryCode);
    }

    /**
     * Pobiera prefix ścieżki po nazwie tabeli
     * @param string $tableName
     * @return string
     * @throws \Mmi\Orm\OrmException
     */
    protected static function _getPathPrefix($tableName)
    {
        $table = explode('_', $tableName);
        //klasy leżą w plikach w /Orm/
        $baseDir = BASE_PATH . '/src/' . ucfirst($table[0]) . '/Orm';
        return $baseDir;
    }

    /**
     * Generuje namespace klasy
     * @param string $tableName
     * @return string
     */
    protected static function _getNamespace($tableName)
    {
        $table = explode('_', $tableName);
        //klasy leżą w namespace'ach Orm w modułach
        return ucfirst($table[0]) . '\\Orm';
    }

    /**
     * Pobiera prefix klasy obiektu
     * @param string $tableName
     * @return string
     * @throws \Mmi\Orm\OrmException
     */
    protected static function _getNamePrefix($tableName)
    {
        $table = explode('_', $tableName);
        $className = '';
        //dodawanie kolejnych zagłębień
        foreach ($table as $section) {
            $className .= ucfirst($section);
        }
        return $className;
    }

    /**
     * Tworzy rekurencyjnie strukturę
     * @param string $path
     * @return string ścieżka wejściowa
     */
    protected static function _mkdirRecursive($path)
    {
        $dirs = explode('/', $path);
        $currentDir = '';
        //tworzenie katalogów po kolei
        foreach ($dirs as $dir) {
            $currentDir .= $dir . '/';
            if (file_exists($currentDir)) {
                continue;
            }
            mkdir($currentDir);
        }
        return $path;
    }

}
