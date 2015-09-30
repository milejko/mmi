<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Solr;

/**
 * Klient Solr
 */
class SolrClient {

	/**
	 * Url do serwera
	 * @var string
	 */
	public $solrServerUrl = null;

	/**
	 * Nazwa core'a
	 * @var string
	 */
	protected $_core = null;

	/**
	 * Metoda dostępu
	 * @var string
	 */
	protected $_method = 'POST';

	/**
	 * Nagłówek
	 * @var string 
	 */
	protected $_header = 'Content-type: text/json';

	/**
	 * Metoda solr
	 * @var string
	 */
	protected $_solrMethod = null;

	/**
	 * Konstruktor
	 * @param string $solrUrl
	 * @param string $core
	 */
	public function __construct($solrUrl, $core) {
		$this->solrServerUrl = $solrUrl;
		$this->_core = $core;
	}

	/**
	 * Metoda odpowiedzialna za reload danego indeksu w przypadku jeżeli zajdą zmiany
	 * w pliku solrconfig.xml lub schema.xml
	 * @return string
	 * @throws SolrException
	 */
	public function reload() {
		$this->_solrMethod = 'cores';

		$opts = [
			'http' => ['ignore_errors' => true]
		];

		$context = stream_context_create($opts);

		//zapytanie
		return file_get_contents($this->solrServerUrl . '/admin/' . $this->_solrMethod . '?wt=json&action=reload&core=' . $this->_core, false, $context);
	}

	/**
	 * Wykonanie metody select na silniku solr
	 *
	 * @param SolrQuery $queryObject
	 * @return String
	 */
	public function select(SolrQuery $queryObject) {
		$this->_solrMethod = 'select';

		$opts = [
			'http' => ['ignore_errors' => true]
		];

		$context = stream_context_create($opts);

		//zapytanie
		return file_get_contents($this->solrServerUrl . '/' . $this->_core . '/' . $this->_solrMethod . '?' . $queryObject->searchUrl(), false, $context);
	}

	/**
	 * Wykonanie insert na danych umieszczonych w solr
	 *
	 * @param \stdClass $obj
	 * @param boolean $commit
	 * @throws SolrException
	 */
	public function insert(\stdClass $obj, $commit = true) {
		$this->_solrMethod = 'update';

		$addObj = new \stdClass();
		$addObj->add = new \stdClass();
		$addObj->add->overwrite = true;
		$addObj->add->doc = $obj;

		$jsonObject = json_encode($addObj);

		//budowanie opcji
		$opts = ['http' =>
			[
				'method' => 'POST',
				'header' => 'Content-type: text/json',
				'content' => $jsonObject,
				'ignore_errors' => true
			]
		];

		//budowanie kontekstu
		$context = stream_context_create($opts);

		//zapytanie
		$response = file_get_contents($this->solrServerUrl . '/' . $this->_core . '/' . $this->_solrMethod, false, $context);
		$responseObject = json_decode($response);
		if (!isset($responseObject->error) && $commit) {
			return $this->commit();
		} 
		if ($commit) {
			throw new SolrException($responseObject->error->msg, $responseObject->error->code);
		}
	}

	/**
	 * Wykonanie insert większej ilości danych
	 * @param array $stdClassArray
	 */
	public function insertAll(array $stdClassArray) {
		foreach ($stdClassArray as $object) {
			if ($object instanceof \stdClass) {
				$this->insert($object, false);
			}
		}
		$this->commit();
	}

	/**
	 * Metoda update danych umieszczonych w indeksie
	 * @param \stdClass $obj
	 */
	public function update(\stdClass $obj) {
		$this->insert($obj);
	}

	/**
	 * Usunięcie danych z indeksu
	 *
	 * @param integer $id
	 * @param boolean $commit
	 * @throws SolrException
	 */
	public function delete($id, $commit = true) {
		$this->_solrMethod = 'update';

		$addObj = new \stdClass($id);
		$addObj->delete = new \stdClass();
		$addObj->delete->id = $id;

		$jsonObject = json_encode($addObj);

		//budowanie opcji
		$opts = ['http' =>
			[
				'method' => 'POST',
				'header' => 'Content-type: text/json',
				'content' => $jsonObject,
				'ignore_errors' => true
			]
		];

		//budowanie kontekstu
		$context = stream_context_create($opts);

		$response = file_get_contents($this->solrServerUrl . '/' . $this->_core . '/' . $this->_solrMethod, false, $context);
		$responseObject = json_decode($response);
		if (!isset($responseObject->error) & $commit) {
			return $this->commit();
		}
		if ($commit) {
			throw new SolrException($responseObject->error->msg, $responseObject->error->code);
		}
	}

	/**
	 * Wykonanie metody commit na indeksie silnika solr
	 * @return string
	 * @throws SolrException
	 */
	public function commit() {
		$this->_solrMethod = 'update';

		//budowanie opcji
		$opts = ['http' =>
			[
				'method' => 'POST',
				'header' => 'Content-type: text/json',
				'ignore_errors' => true
			]
		];

		//budowanie kontekstu
		$context = stream_context_create($opts);

		//zapytanie
		$response = file_get_contents($this->solrServerUrl . '/' . $this->_core . '/' . $this->_solrMethod . '?commit=true', false, $context);
		$responseObject = json_decode($response);
		if (!isset($responseObject->error)) {
			return $response;
		}
		throw new SolrException($responseObject->error->msg, $responseObject->error->code);
	}

}
