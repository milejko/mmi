<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Db\Adapter;

use Mmi\Db\DbException;

/**
 * Abstrakcyjna klasa adaptera PDO
 */
abstract class PdoAbstract {

	/**
	 * Obiekt \PDO do pobierania danych
	 * @var \PDO
	 */
	protected $_downstreamPdo;

	/**
	 * Obiekt \PDO do wysyłania daych
	 * @var \PDO
	 */
	protected $_upstreamPdo;

	/**
	 * Konfiguracja
	 * @var \Mmi\Db\Config
	 */
	protected $_config;

	/**
	 * Stan połączenia
	 * @var boolean
	 */
	protected $_connected = false;

	/**
	 * Stan transakcji
	 * @var boolean
	 */
	protected $_transactionInProgress = false;

	/**
	 * Otacza nazwę pola odpowiednimi znacznikami
	 * @param string $fieldName nazwa pola
	 * @return string
	 */
	abstract public function prepareField($fieldName);

	/**
	 * Otacza nazwę tabeli odpowiednimi znacznikami
	 * @param string $tableName nazwa tabeli
	 * @return string
	 */
	abstract public function prepareTable($tableName);

	/**
	 * Zwraca informację o kolumnach tabeli
	 * @param string $tableName nazwa tabeli
	 * @param array $schema schemat
	 * @return array
	 */
	abstract public function tableInfo($tableName, $schema = null);

	/**
	 * Listuje tabele w schemacie bazy danych
	 * @param string $schema
	 * @return array
	 */
	abstract public function tableList($schema = null);

	/**
	 * Tworzy konstrukcję sprawdzającą null w silniku bazy danych
	 * @param string $fieldName nazwa pola
	 * @param boolean $positive sprawdza czy null, lub czy nie null
	 * @return string
	 */
	abstract public function prepareNullCheck($fieldName, $positive = true);

	/**
	 * Tworzy konstrukcję sprawdzającą ILIKE, jeśli dostępna w silniku
	 * @param string $fieldName nazwa pola
	 * @return string
	 */
	abstract public function prepareIlike($fieldName);

	/**
	 * Ustawia schemat
	 * @param string $schemaName nazwa schematu
	 * @return \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	abstract public function selectSchema($schemaName);

	/**
	 * Ustawia domyślne parametry dla importu (długie zapytania)
	 * @return \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	abstract public function setDefaultImportParams();

	/**
	 * Zwraca nazwę sekwencji dla tabeli
	 * @param string $tableName nazwa tabeli
	 * @return string
	 */
	public function prepareSequenceName($tableName) {
		return $tableName . '_id_seq';
	}

	/**
	 * Konstruktor wczytujący konfigurację
	 * @param \Mmi\Db\Config $config
	 */
	public function __construct(\Mmi\Db\Config $config) {
		$this->_config = $config;
	}

	/**
	 * Zwraca konfigurację
	 * @return \Mmi\Db\Config
	 */
	public final function getConfig() {
		return $this->_config;
	}

	/**
	 * Nieistniejące metody
	 * @param string $method
	 * @param array $params
	 * @throws DbException
	 */
	public final function __call($method, $params) {
		throw new DbException(get_called_class() . ': method not found: ' . $method);
	}

	/**
	 * Tworzy połączenie z bazą danych
	 * @return \Mmi\Db\Adapter\Pdo\PdoAbstract
	 */
	public function connect() {
		//event łączenia
		\Mmi\App\FrontController::getInstance()->getProfiler()->event(get_called_class() . ': connect', 0);

		//nowy obiekt PDO do odczytu danych
		$this->_downstreamPdo = new \PDO(
			$this->_config->driver . ':host=' . $this->_config->host . ';port=' . $this->_config->port . ';dbname=' . $this->_config->name . ';charset=utf8', $this->_config->user, $this->_config->password, [\PDO::ATTR_PERSISTENT => $this->_config->persistent]
		);

		//nowy obiekt pdo do zapisu danych
		$this->_upstreamPdo = new \PDO(
			$this->_config->driver . ':host=' . ($this->_config->upstreamHost ? $this->_config->upstreamHost : $this->_config->host) . ';port=' . ($this->_config->upstreamPort ? $this->_config->upstreamPort : $this->_config->port) . ';dbname=' . $this->_config->name . ';charset=utf8', $this->_config->user, $this->_config->password, [\PDO::ATTR_PERSISTENT => $this->_config->persistent]
		);

		//zmiana stanu na połączony
		$this->_connected = true;
		return $this;
	}

	/**
	 * Zwraca opakowaną cudzysłowami wartość
	 * @see \PDO::quote()
	 * @see \PDO::PARAM_STR
	 * @see \PDO::PARAM_INT
	 * @param string $value wartość
	 * @param string $paramType
	 * @return string
	 */
	public final function quote($value, $paramType = \PDO::PARAM_STR) {
		//łączy jeśli niepołączony
		if (!$this->_connected) {
			$this->connect();
		}
		//dla szczególnych typów: null int i bool nie opakowuje
		switch (gettype($value)) {
			case 'NULL':
				return 'NULL';
			case 'integer':
				return intval($value);
			case 'boolean':
				return $value ? 'true' : 'false';
		}
		//quote z PDO
		return $this->_downstreamPdo->quote($value, $paramType);
	}

	/**
	 * Wydaje zapytanie \PDO prepare, execute
	 * rzuca wyjątki
	 * @see \PDO::prepare()
	 * @see \PDO::execute()
	 * @param string $sql zapytanie
	 * @param array $bind tabela w formacie akceptowanym przez \PDO::prepare()
	 * @throws DbException
	 * @return \PDO_Statement
	 */
	public function query($sql, array $bind = []) {
		//łączy jeśli niepołączony
		if (!$this->_connected) {
			$this->connect();
		}
		//czas startu na potrzeby profilera
		$start = microtime(true);
		//wybór PDO (zapis lub odczyt)
		$pdo = (preg_match('/^SELECT/i', $sql) && !$this->_transactionInProgress) ? $this->_downstreamPdo : $this->_upstreamPdo;
		//przygotowywanie zapytania
		$statement = $pdo->prepare($sql);
		//błędne zapytanie
		if (!$statement) {
			$error = $pdo->errorInfo();
			throw new DbException(get_called_class() . ': ' . (isset($error[2]) ? $error[2] : $error[0]) . ' --- ' . $sql);
		}
		//wiązanie parametrów do zapytania
		foreach ($bind as $key => $param) {
			//domyślnie typ jako string
			$type = \PDO::PARAM_STR;
			//jeśli bool to bool
			if (is_bool($param)) {
				$type = \PDO::PARAM_BOOL;
			}
			//jeśli kluczem jest liczba (przy insertach), zwiększamy licznik o 1
			if (is_int($key)) {
				$key = $key + 1;
			}
			//wiązanie wartości
			$statement->bindValue($key, $param, $type);
		}
		//wykonywanie zapytania
		$result = $statement->execute();
		//przypisanie rezultatu do zmiennej w statement
		$statement->result = $result;
		//jeśli rezultat to nie 1, wyrzucony zostaje wyjątek bazodanowy
		if ($result != 1) {
			$error = $statement->errorInfo();
			$error = isset($error[2]) ? $error[2] : $error[0];
			throw new DbException(get_called_class() . ': ' . $error . ' --- ' . $sql);
		}
		//jeśli profiler włączony, dodanie eventu
		\Mmi\App\FrontController::getInstance()->getProfiler()->eventQuery($statement, $bind, microtime(true) - $start);
		return $statement;
	}

	/**
	 * Zwraca ostatnio wstawione ID
	 * @param string $name opcjonalnie nazwa serii (ważne w PostgreSQL)
	 * @return mixed
	 */
	public function lastInsertId($name = null) {
		//łączy jeśli niepołączony
		if (!$this->_connected) {
			$this->connect();
		}
		return $this->_upstreamPdo->lastInsertId($name);
	}

	/**
	 * Zwraca wszystkie rekordy (rządki)
	 * @param string $sql zapytanie
	 * @param array $bind tabela w formacie akceptowanym przez \PDO::prepare()
	 * @return array
	 */
	public final function fetchAll($sql, array $bind = []) {
		return $this->query($sql, $bind)->fetchAll(\PDO::FETCH_NAMED);
	}

	/**
	 * Zwraca pierwszy rekord (rządek)
	 * @param string $sql zapytanie
	 * @param array $bind tabela w formacie akceptowanym przez \PDO::prepare()
	 * @return array
	 */
	public final function fetchRow($sql, array $bind = []) {
		return $this->query($sql, $bind)->fetch(\PDO::FETCH_NAMED);
	}

	/**
	 * Zwraca pojedynczą wartość (krotkę)
	 * @param string $sql zapytanie
	 * @param array $bind tabela w formacie akceptowanym przez \PDO::prepare()
	 * @return array
	 */
	public final function fetchOne($sql, array $bind = []) {
		return $this->query($sql, $bind)->fetch(\PDO::FETCH_NUM);
	}

	/**
	 * Wstawianie rekordu
	 * @param string $table nazwa tabeli
	 * @param array $data tabela w postaci: klucz => wartość
	 */
	public function insert($table, array $data = []) {
		$fields = '';
		$values = '';
		$bind = [];
		//wiązanie placeholderów "?" w zapytaniu z parametrami do wstawienia
		foreach ($data as $key => $value) {
			$fields .= $this->prepareField($key) . ', ';
			$values .= '?, ';
			$bind[] = $value;
		}
		//budowanie wstawiającego SQL
		$sql = 'INSERT INTO ' . $this->prepareTable($table) . ' (' . rtrim($fields, ', ') . ') VALUES(' . rtrim($values, ', ') . ')';
		return $this->query($sql, $bind)->rowCount();
	}

	/**
	 * Wstawianie wielu rekordów
	 * @param string $table nazwa tabeli
	 * @param array $data tabela tabel w postaci: klucz => wartość
	 * @return integer
	 */
	public function insertAll($table, array $data = []) {
		$fields = '';
		$fieldsCompleted = false;
		$values = '';
		$bind = [];
		//dla każdego rekordu te same operacje
		foreach ($data as $row) {
			if (empty($row)) {
				continue;
			}
			$cur = '';
			//wiązanie placeholderów "?" w zapytaniu z parametrami do wstawienia
			foreach ($row as $key => $value) {
				if (!$fieldsCompleted) {
					$fields .= $this->prepareField($key) . ', ';
				}
				$cur .= '?, ';
				$bind[] = $value;
			}
			$values .= '(' . rtrim($cur, ', ') . '), ';
			$fieldsCompleted = true;
		}
		$sql = 'INSERT INTO ' . $this->prepareTable($table) . ' (' . rtrim($fields, ', ') . ') VALUES ' . rtrim($values, ', ');
		return $this->query($sql, $bind)->rowCount();
	}

	/**
	 * Aktualizacja rekordów
	 * @param string $table nazwa tabeli
	 * @param array $data tabela w postaci: klucz => wartość
	 * @param array $whereBind warunek w postaci zagnieżdżonego bind
	 * @return integer
	 */
	public function update($table, array $data = [], $where = '', array $whereBind = []) {
		$fields = '';
		$bind = [];
		//jeśli brak danych wyjście z metody
		if (empty($data)) {
			return 1;
		}
		//wiązanie parametrów
		foreach ($data as $key => $value) {
			$bindKey = PdoBindHelper::generateBindKey();
			$fields .= $this->prepareField($key) . ' = :' . $bindKey . ', ';
			$bind[$bindKey] = $value;
		}
		//budowanie zapytania aktualizującego
		$sql = 'UPDATE ' . $this->prepareTable($table) . ' SET ' . rtrim($fields, ', ') . ' ' . $where;
		return $this->query($sql, array_merge($bind, $whereBind))->rowCount();
	}

	/**
	 * Kasowanie rekordu
	 * @param string $table nazwa tabeli
	 * @param array $whereBind warunek w postaci zagnieżdżonego bind
	 * @return integer
	 */
	public function delete($table, $where = '', array $whereBind = []) {
		return $this->query('DELETE FROM ' . $this->prepareTable($table) . ' ' . $where, $whereBind)
				->rowCount();
	}

	/**
	 * Pobieranie rekordów
	 * @param string $fields pola do wybrania
	 * @param string $from część zapytania po FROM
	 * @param string $where warunek
	 * @param string $order sortowanie
	 * @param int $limit limit
	 * @param int $offset ofset
	 * @param array $whereBind parametry
	 * @return array
	 */
	public function select($fields = '*', $from = '', $where = '', $groupBy = '', $order = '', $limit = null, $offset = null, array $whereBind = []) {
		$sql = 'SELECT' .
			' ' . $fields .
			' FROM' .
			' ' . $from .
			' ' . $where .
			' ' . $groupBy .
			' ' . $order .
			' ' . $this->prepareLimit($limit, $offset);
		return $this->fetchAll($sql, $whereBind);
	}

	/**
	 * Rozpoczyna transakcję
	 */
	public final function beginTransaction() {
		//łączenie jeśli niepołączony
		if (!$this->_connected) {
			$this->connect();
		}
		//rozpoczęcie transakcji
		$this->_transactionInProgress = true;
		return $this->_upstreamPdo->beginTransaction();
	}

	/**
	 * Zatwierdza transakcję
	 */
	public final function commit() {
		//łączenie jeśli niepołączony
		if (!$this->_connected) {
			$this->connect();
		}
		//zakończenie transakcji z zatwierdzeniem
		$this->_transactionInProgress = false;
		return $this->_upstreamPdo->commit();
	}

	/**
	 * Odrzuca transakcję
	 */
	public final function rollBack() {
		//łączenie jeśli niepołączony
		if (!$this->_connected) {
			$this->connect();
		}
		//zakończenie transakcji z cofnięciem
		$this->_transactionInProgress = false;
		return $this->_upstreamPdo->rollBack();
	}

	/**
	 * Tworzy warunek limit
	 * @param int $limit
	 * @param int $offset
	 * @return string
	 */
	public function prepareLimit($limit = null, $offset = null) {
		//wyjście jeśli brak limitu
		if (!($limit > 0)) {
			return;
		}
		//limit z offsetem
		if ($offset > 0) {
			return 'LIMIT ' . intval($offset) . ', ' . intval($limit);
		}
		//sam limit
		return 'LIMIT ' . intval($limit);
	}

	/**
	 * Konwertuje do tabeli asocjacyjnej meta dane tabel
	 * @param array $meta meta data
	 * @return array
	 */
	protected function _associateTableMeta(array $meta) {
		$associativeMeta = [];
		foreach ($meta as $column) {
			//przekształcanie odpowiedzi do standardowej postaci
			$associativeMeta[$column['name']] = [
				'dataType' => $column['dataType'],
				'maxLength' => $column['maxLength'],
				'null' => ($column['null'] == 'YES') ? true : false,
				'default' => $column['default']
			];
		}
		return $associativeMeta;
	}

}
