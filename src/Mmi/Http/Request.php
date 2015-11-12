<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Http;

class Request extends \Mmi\DataObject {

	/**
	 * Konstruktor, pozwala podać zmienne requestu
	 * @param array $data zmienne requestu
	 */
	public function __construct(array $data = []) {
		$this->setParams($data);
	}

	/**
	 * Zwraca Content-Type żądania
	 * @return string
	 */
	public function getContentType() {
		return filter_input(INPUT_SERVER, 'CONTENT_TYPE', FILTER_SANITIZE_SPECIAL_CHARS);
	}

	/**
	 * Zwraca metodę żądania (np. GET, POST, PUT)
	 * @return string
	 */
	public function getRequestMethod() {
		return filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS);
	}

	/**
	 * Pobiera nagłówek żądania
	 * @param string $name np. Accept-Encoding
	 * @return string
	 */
	public function getHeader($name) {
		$headerName = strtoupper(preg_replace("/[^a-zA-Z0-9_]/", '_', $name));
		return filter_input(INPUT_SERVER, 'HTTP_' . $headerName, FILTER_SANITIZE_SPECIAL_CHARS);
	}

	/**
	 * Zwraca zmienne POST w postaci tabeli
	 * @return RequestPost
	 */
	public function getPost() {
		return new RequestPost($_POST);
	}
	
	/**
	 * Zwraca zmienne GET w postaci tabeli
	 * @return RequestGet
	 */
	public function getGet() {
		return new RequestGet($_GET);
	}

	/**
	 * Pobiera informacje o zuploadowanych plikach FILES
	 * @return RequestFiles
	 */
	public function getFiles() {
		return new RequestFiles($_FILES);
	}

	/**
	 * Zwraca referer, lub stronę główną jeśli brak
	 * @return string
	 */
	public function getReferer() {
		return \Mmi\App\FrontController::getInstance()->getEnvironment()->httpReferer;
	}

	/**
	 * Zwraca moduł
	 * @return string
	 */
	public function getModuleName() {
		return $this->__get('module');
	}

	/**
	 * Zwraca kontroler
	 * @return string
	 */
	public function getControllerName() {
		return $this->__get('controller');
	}

	/**
	 * Zwraca akcję
	 * @return string
	 */
	public function getActionName() {
		return $this->__get('action');
	}

	/**
	 * Ustawia moduł
	 * @param string $value
	 * @return \Mmi\Http\Request
	 */
	public function setModuleName($value) {
		$this->__set('module', $value);
		return $this;
	}

	/**
	 * Ustawia kontroler
	 * @param string $value
	 * @return \Mmi\Http\Request
	 */
	public function setControllerName($value) {
		$this->__set('controller', $value);
		return $this;
	}

	/**
	 * Ustawia akcję
	 * @param string $value
	 * @return \Mmi\Http\Request
	 */
	public function setActionName($value) {
		$this->__set('action', $value);
		return $this;
	}

}
