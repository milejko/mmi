<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\JsonRpc;

class JsonResponse {

	/**
	 * Wersja JSON-RPC
	 * @var string
	 */
	public $jsonrpc = '2.0';

	/**
	 * ID odpowiedzi
	 * @var integer
	 */
	public $id;

	/**
	 * Rezultat
	 * @var string 
	 */
	public $result;

	/**
	 * Błąd
	 * @var string
	 */
	public $error;

	/**
	 * Ustawia obiekt na podstawie JSON'a
	 * @param string $data
	 * @return \Mmi\JsonRpc\JsonResponse
	 */
	public function setFromJson($data) {
		$response = json_decode($data);
		$this->jsonrpc = isset($response->jsonrpc) ? $response->jsonrpc : null;
		$this->id = isset($response->id) ? $response->id : null;
		$this->result = isset($response->result) ? $response->result : null;
		$this->error = isset($response->error) ? $response->error : null;
		return $this;
	}

	/**
	 * Konwersja do JSON'a
	 * @return string
	 */
	public function toJson() {
		return json_encode($this);
	}

}
