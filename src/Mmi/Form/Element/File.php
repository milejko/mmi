<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

class File extends ElementAbstract {

	/**
	 * Informacje o zuploadowanym pliku
	 * @var \Mmi\Controller\Request\File[]
	 */
	private $_files = [];

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		return '<input type="file" ' . $this->_getHtmlOptions() . '/>';
	}

	/**
	 * Zbiera pliki z tabeli $_FILES jeśli istnieją jakieś pliki dla tego pola
	 * @return \Mmi\Form\Element\File
	 */
	public function init() {
		//nazwa pola
		$fieldName = $this->getOption('name');
		//tablica obiektów file
		$files = \Mmi\App\FrontController::getInstance()->getRequest()->getFiles();
		//brak pliku dla tego elementu formularza
		if (!$files->{$fieldName}) {
			return;
		}
		$this->_files = $files->{$fieldName};
		//opakowanie w array jeśli plik jest jeden
		if ($this->_files instanceof \Mmi\Controller\Request\File) {
			$this->_files = [$this->_files];
		}
		return $this;
	}

	/**
	 * Pobiera informacje o wgranym pliku (jeśli istnieje)
	 * @return \Mmi\Controller\Request\File[]
	 */
	public function getFiles() {
		return $this->_files;
	}

	/**
	 * Zwraca czy plik został zuploadowany do tego pola
	 * @return boolean
	 */
	public function isUploaded() {
		return !empty($this->_files);
	}

}
