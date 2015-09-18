<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Controller\Request;

/**
 * Klasa plików
 * @method File[] toArray() Zwraca tablicę obiektów plików \Mmi\Controller\Request\File
 */
class Files extends \Mmi\DataObject {

	/**
	 * Konstruktor
	 * @param array $data dane z FILES
	 */
	public function __construct(array $data = []) {
		//obsługa uploadu plików
		$this->setParams($this->_handleUpload($data));
	}

	/**
	 * Zwraca tablicę obiektów plików
	 * @param array $data
	 * @return array
	 */
	protected function _handleUpload(array $data) {
		$files = [];
		foreach ($data as $fieldName => $fieldFiles) {
			if (!isset($files[$fieldName])) {
				$files[$fieldName] = [];
			}
			//pojedynczy plik
			if (null !== ($file = $this->_handleSingleUpload($fieldFiles))) {
				$files[$fieldName][] = $file;
				continue;
			}
			//obsługa multiuploadu HTML5
			$files[$fieldName] = $this->_handleMultiUpload($fieldFiles);
		}
		return $files;
	}

	/**
	 * Obsługa pojedynczego uploadu
	 * @param array $fileData dane pliku
	 * @return \Mmi\Controller\Request\File
	 */
	protected function _handleSingleUpload(array $fileData) {
		//jeśli nazwa jest tablicą, oznacza to wielokrotny upload HTML5
		if (is_array($fileData['name'])) {
			return;
		}
		//brak 
		if (!isset($fileData['tmp_name']) || $fileData['tmp_name'] == '') {
			return;
		}
		$fileData['type'] = \Mmi\FileSystem::mimeType($fileData['tmp_name']);
		return new File($fileData);
	}

	/**
	 * Obsługa uploadu wielu plików (HTML5)
	 * @param array $fileData dane plików
	 * @return \Mmi\Controller\Request\File[]
	 */
	protected function _handleMultiUpload(array $fileData) {
		$files = [];
		//upload wielokrotny html5
		for ($i = 0, $count = count($fileData); $i < $count; $i++) {
			if (!isset($fileData['tmp_name'][$i]) || !$fileData['tmp_name'][$i]) {
				continue;
			}
			//tworzenie obiektów plików
			$files[] = new File([
				'name' => $fileData['name'][$i],
				'tmp_name' => $fileData['tmp_name'][$i],
				'size' => $fileData['size'][$i]
			]);
		}
		return $files;
	}

}
