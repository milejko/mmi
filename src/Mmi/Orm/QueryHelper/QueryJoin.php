<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm\QueryHelper;

use Mmi\Orm\Query;

/**
 * Klasa połączenia w zapytaniu
 */
class QueryJoin {

	/**
	 * Referencja do nadrzędnego zapytania
	 * @var Query
	 */
	protected $_query;

	/**
	 * Nazwa tabeli
	 * @var string
	 */
	protected $_tableName;

	/**
	 * Nazwa do której wykonać łączenie
	 * @var string
	 */
	protected $_targetTableName;

	/**
	 * Typ złączenia 'JOIN' 'LEFT JOIN' 'INNER JOIN' 'RIGHT JOIN'
	 * @var string
	 */
	protected $_type;
	
	/**
	 * Alias złączenia
	 * @var string
	 */
	protected $_alias;

	/**
	 * Ustawia parametry połączenia
	 * @param Query $query
	 * @param string $tableName nazwa tabeli
	 * @param string $type typ złączenia: 'JOIN', 'LEFT JOIN', 'INNER JOIN', 'RIGHT JOIN'
	 * @param string $targetTableName opcjonalna tabela do której złączyć
	 * @param string $alias alias złączenia
	 */
	public function __construct(Query $query, $tableName, $type = 'JOIN', $targetTableName = null, $alias = null) {
		$this->_query = $query;
		$this->_tableName = $tableName;
		$this->_targetTableName = $targetTableName;
		$this->_type = $type;
		$this->_alias = $alias;
	}

	/**
	 * Warunek złączenia
	 * @param string $localKeyName nazwa lokalnego klucza
	 * @param string $joinedKeyName nazwa klucza w łączonej tabeli
	 * @return Query
	 */
	public function on($localKeyName, $joinedKeyName = 'id') {
		$this->_query->getQueryCompile()->joinSchema[] = [$this->_tableName, $joinedKeyName, $localKeyName, $this->_targetTableName, $this->_type, $this->_alias];
		return $this->_query;
	}

}
