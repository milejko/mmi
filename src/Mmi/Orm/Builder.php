<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm;

/**
 * Klasa budowniczego struktur DAO/Record/Query
 */
class Builder {

	/**
	 * Renderuje DAO, Record i Query dla podanej nazwy tabeli
	 * @param string $tableName
	 * @throws Exception
	 */
	public static function buildFromTableName($tableName) {
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
	protected static function _updateRecord($tableName) {
		//prefixy nazw
		$pathPrefix = self::_getPathPrefixByTableName($tableName);
		$classPrefix = self::_getClassNamePrefixByTableName($tableName);
		$recordCode = '<?php' . "\n\n" .
			'namespace ' . $classPrefix . ";\n\n" .
			'class Record extends \Mmi\Orm\Record {' .
			"\n\n" .
			'}' . "\n";
		//ścieżka do pliku
		$path = self::_mkdirRecursive($pathPrefix . '/Record.php');
		//wczytanie istniejącego rekordu
		if (file_exists($path)) {
			$recordCode = file_get_contents($path);
		}
		//odczyt struktury tabeli
		$structure = \Mmi\Orm::getTableStructure($tableName);
		//błędna struktrura lub brak
		if (empty($structure)) {
			throw new\Exception('\Mmi\Orm\Builder: no table found, or table invalid: ' . $tableName);
		}
		$variableString = "\n";
		//generowanie pól rekordu
		foreach ($structure as $fieldName => $fieldDetails) {
			$variables[] = \Mmi\Orm\Convert::underscoreToCamelcase($fieldName);
			$variableString .= "\t" . 'public $' . \Mmi\Orm\Convert::underscoreToCamelcase($fieldName) . ";\n";
		}
		//sprawdzanie istnienia pól rekordu
		if (preg_match_all('/\tpublic \$([a-zA-Z0-9\_]+)[\;|\s\=]/', $recordCode, $codeVariables) && isset($codeVariables[1])) {
			//za dużo względem bazy
			$diffRecord = array_diff($codeVariables[1], $variables);
			//brakujące względem DB
			$diffDb = array_diff($variables, $codeVariables[1]);
			//pola się nie zgadzają
			if (!empty($diffRecord) || !empty($diffDb)) {
				throw new \Exception('RECORD for: "' . $tableName . '" has invalid fields: ' . implode(', ', $diffRecord) . ', and missing: ' . implode(',', $diffDb));
			}
			return;
		}
		$recordCode = preg_replace('/(class Record extends [\\a-zA-Z0-9]+\s\{?\r?\n?)/', '$1' . $variableString, $recordCode);
		//zapis pliku
		file_put_contents($path, $recordCode);
	}

	/**
	 * Tworzy lub aktualizuje pole query
	 * @param string $tableName
	 */
	protected static function _updateQueryField($tableName) {
		//prefixy nazw
		$pathPrefix = self::_getPathPrefixByTableName($tableName);
		$classPrefix = self::_getClassNamePrefixByTableName($tableName);
		$queryClassName = $classPrefix . '\Query';
		//odczyt struktury
		$structure = \Mmi\Orm::getTableStructure($tableName);
		$methods = '';
		//budowanie komentarzy do metod
		foreach ($structure as $fieldName => $fieldDetails) {
			$fieldName = ucfirst(\Mmi\Orm\Convert::underscoreToCamelcase($fieldName));
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
			'namespace ' . $classPrefix . '\Query' . ";\n\n" .
			'/**' . "\n" .
			' * @method \\' . $queryClassName . ' equals($value)' . "\n" .
			' * @method \\' . $queryClassName . ' notEquals($value)' . "\n" .
			' * @method \\' . $queryClassName . ' greater($value)' . "\n" .
			' * @method \\' . $queryClassName . ' less($value)' . "\n" .
			' * @method \\' . $queryClassName . ' greaterOrEquals($value)' . "\n" .
			' * @method \\' . $queryClassName . ' lessOrEquals($value)' . "\n" .
			' * @method \\' . $queryClassName . ' like($value)' . "\n" .
			' * @method \\' . $queryClassName . ' ilike($value)' . "\n" .
			$methods . 
			' */' . "\n" .
			'class Field extends \Mmi\Orm\Query\Field {' .
			"\n\n" .
			'}' . "\n";
		//zapis pliku
		file_put_contents(self::_mkdirRecursive($pathPrefix . '/Query/Field.php'), $queryCode);
	}

	/**
	 * Tworzy lub aktualizuje obiekt złączenia (JOIN)
	 * @param string $tableName
	 */
	protected static function _updateQueryJoin($tableName) {
		//prefixy nazw
		$pathPrefix = self::_getPathPrefixByTableName($tableName);
		$classPrefix = self::_getClassNamePrefixByTableName($tableName);
		$queryClassName = $classPrefix . '\Query';
		//anotacja dla metody on()
		$queryCode = '<?php' . "\n\n" .
			'namespace ' . $classPrefix . '\Query' . ";\n\n" .
			'/**' . "\n" .
			' * @method \\' . $queryClassName . ' on($localKeyName, $joinedKeyName = \'id\')' . "\n" .
			' */' . "\n" .
			'class Join extends \Mmi\Orm\Query\Join {' .
			"\n\n" .
			'}' . "\n";
		//zapis pliku
		file_put_contents(self::_mkdirRecursive($pathPrefix . '/Query/Join.php'), $queryCode);
	}

	/**
	 * Tworzy, lub aktualizuje zapytanie
	 * @param string $tableName
	 */
	protected static function _updateQuery($tableName) {
		//prefixy nazw
		$pathPrefix = self::_getPathPrefixByTableName($tableName);
		$classPrefix = self::_getClassNamePrefixByTableName($tableName);
		//nazwa klasy
		$className = $classPrefix . '\Query';
		//nazwa klasy pola
		$fieldClassName = $classPrefix . '\Query\Field';
		//nazwa klasy złączenia
		$joinClassName = $classPrefix . '\Query\Join';
		//nazwa rekordu
		$recordClassName = $classPrefix . '\Record';

		//ścieżka
		$path = $pathPrefix . '/Query.php';
		self::_mkdirRecursive($path);
		//kod zapytania
		$queryCode = '{'
			. "\n\n\t"
			. 'protected $_tableName = \''
			. $tableName . '\';'
			. "\n\n" 
			. "\t" . '/**' . "\n"
			. "\t" . ' * @return \\' . $className . "\n"
			. "\t" . ' */' . "\n"
			. "\t" . 'public static function factory($tableName = null)' . " {\n"
			. "\t\t" . 'return new self($tableName);' . "\n"
			. "\t}\n\n}\n";
		//wczytanie istniejącego rekordu
		if (file_exists($path)) {
			$queryCode = file_get_contents($path);
		}
		//odczyt struktury
		$structure = \Mmi\Orm::getTableStructure($tableName);
		//pusta, lub błędna struktura
		if (empty($structure)) {
			throw new \Exception('\Mmi\Orm\Builder: no table found, or table invalid: ' . $tableName);
		}

		$methods = '';
		//budowanie komentarzy do metod
		foreach ($structure as $fieldName => $fieldDetails) {
			$fieldName = ucfirst(\Mmi\Orm\Convert::underscoreToCamelcase($fieldName));
			//metody where... np. whereActive()
			$methods .= ' * @method \\' . $fieldClassName . ' where' . $fieldName . '()' . "\n";
			//metody andField... np. andFieldActive()
			$methods .= ' * @method \\' . $fieldClassName . ' andField' . $fieldName . '()' . "\n";
			//orField
			$methods .= ' * @method \\' . $fieldClassName . ' orField' . $fieldName . '()' . "\n";
			//orderAsc
			$methods .= ' * @method \\' . $className . ' orderAsc' . $fieldName . '()' . "\n";
			//orderDesc
			$methods .= ' * @method \\' . $className . ' orderDesc' . $fieldName . '()' . "\n";
			//groupBy
			$methods .= ' * @method \\' . $className . ' groupBy' . $fieldName . '()' . "\n";
		}
		$queryHead = '<?php' . "\n\n" .
			'namespace ' . $classPrefix . ";\n\n" .
			'//<editor-fold defaultstate="collapsed" desc="' . $tableName . ' Query">' . "\n" .
			'/**' . "\n" .
			' * @method \\' . $className . ' limit($limit = null)' . "\n" .
			' * @method \\' . $className . ' offset($offset = null)' . "\n" .
			' * @method \\' . $className . ' orderAsc($fieldName, $tableName = null)' . "\n" .
			' * @method \\' . $className . ' orderDesc($fieldName, $tableName = null)' . "\n" .
			' * @method \\' . $className . ' andQuery(\Mmi\Orm\Query $query)' . "\n" .
			' * @method \\' . $className . ' whereQuery(\Mmi\Orm\Query $query)' . "\n" .
			' * @method \\' . $className . ' orQuery(\Mmi\Orm\Query $query)' . "\n" .
			' * @method \\' . $className . ' resetOrder()' . "\n" .
			' * @method \\' . $className . ' resetWhere()' . "\n" .
			$methods .
			' * @method \\' . $fieldClassName . ' andField($fieldName, $tableName = null)' . "\n" .
			' * @method \\' . $fieldClassName . ' where($fieldName, $tableName = null)' . "\n" .
			' * @method \\' . $fieldClassName . ' orField($fieldName, $tableName = null)' . "\n" .
			' * @method \\' . $joinClassName . ' join($tableName, $targetTableName = null)' . "\n" .
			' * @method \\' . $joinClassName . ' joinLeft($tableName, $targetTableName = null)' . "\n" .
			' * @method \\' . $recordClassName . '[] find()' . "\n" .
			' * @method \\' . $recordClassName . ' findFirst()' . "\n" .
			' * @method \\' . $recordClassName . ' findPk($value)' . "\n" .
			' */' . "\n" . '//</editor-fold>' . "\n" . 'class Query extends \Mmi\Orm\Query ';
		$queryCode = $queryHead . substr($queryCode, strpos($queryCode, '{'));
		file_put_contents($path, $queryCode);
	}

	/**
	 * Pobiera prefix ścieżki po nazwie tabeli
	 * @param string $tableName
	 * @return string
	 * @throws Exception
	 */
	protected static function _getPathPrefixByTableName($tableName) {
		$table = explode('_', $tableName);
		//klasy leżą w plikach w /Orm/
		$baseDir = BASE_PATH . '/src/' . ucfirst($table[0]) . '/Orm/';
		unset($table[0]);
		//dodawanie kolejnych zagłębień
		foreach ($table as $subFolder) {
			$baseDir .= ucfirst($subFolder) . '/';
		}
		return rtrim($baseDir, '/');
	}

	/**
	 * Pobiera prefix klasy obiektu
	 * @param string $tableName
	 * @return string
	 * @throws Exception
	 */
	protected static function _getClassNamePrefixByTableName($tableName) {
		$table = explode('_', $tableName);
		//klasy leżą w namespace'ach Orm w modułach
		$className = ucfirst($table[0]) . '\\Orm\\';
		unset($table[0]);
		//dodawanie kolejnych zagłębień
		foreach ($table as $subFolder) {
			$className .= ucfirst($subFolder) . '\\';
		}
		return rtrim($className, '\\');
	}

	/**
	 * Tworzy rekurencyjnie strukturę
	 * @param string $path
	 * @return string ścieżka wejściowa
	 */
	protected static function _mkdirRecursive($path) {
		//ekstrakcja nazwy katalogu
		$dirPath = dirname($path);
		$dirs = explode('/', $dirPath);
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
