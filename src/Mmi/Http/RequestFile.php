<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Http;

class RequestFile {
	
	public $name;
	
	public $tmpName;
	
	public $size;
	
	public $type;
	
	/**
	 * Konstruktor
	 * @throws \Mmi\Http\HttpException
	 * @param array $data
	 */
	public function __construct(array $data) {
		//brak nazwy
		if (!isset($data['name'])) {
			throw new HttpException('RequestFile: name not specified');
		}
		//brak tmp_name
		if (!isset($data['tmp_name'])) {
			throw new HttpException('RequestFile: tmp_name not specified');
		}
		//brak rozmiaru
		if (!isset($data['size'])) {
			throw new HttpException('RequestFile: size not specified');
		}
		$this->name = $data['name'];
		$this->type = \Mmi\FileSystem::mimeType($data['tmp_name']);
		$this->tmpName = $data['tmp_name'];
		$this->size = $data['size'];
	}
	
}