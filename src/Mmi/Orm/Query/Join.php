<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm\Query;

class Join {

	/**
	 * Referencja do nadrzędnego zapytania
	 * @var \Mmi\Orm\Query
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
	 * Ustawia parametry połączenia
	 * @param \Mmi\Orm\Query $query
	 * @param string $tableName nazwa tabeli
	 * @param string $type typ złączenia: 'JOIN', 'LEFT JOIN', 'INNER JOIN', 'RIGHT JOIN'
	 * @param string $targetTableName opcjonalna tabela do której złączyć
	 */
	public function __construct(\Mmi\Orm\Query $query, $tableName, $type = 'JOIN', $targetTableName = null) {
		$this->_query = $query;
		$this->_tableName = $tableName;
		$this->_targetTableName = $targetTableName;
		$this->_type = $type;
	}

	/**
	 * Warunek złączenia
	 * @param string $localKeyName nazwa lokalnego klucza
	 * @param string $joinedKeyName nazwa klucza w łączonej tabeli
	 * @return \Mmi\Orm\Query
	 */
	public function on($localKeyName, $joinedKeyName = 'id') {
		$this->_query->getQueryCompile()->joinSchema[$this->_tableName] = [$joinedKeyName, $localKeyName, $this->_targetTableName, $this->_type];
		return $this->_query;
	}

}
