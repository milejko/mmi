<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Filter;

/**
 * Abstrakcyjna klasa filtra
 */
abstract class FilterAbstract extends \Mmi\OptionObject {
	
	/**
	 * Konstruktor ustawia opcje
	 * @param array $options
	 */
	public final function __construct(array $options = []) {
		$this->setOptions($options);
	}

	/**
	 * Zwraca przefiltrowaną wartość
	 * @param mixed $value
	 * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
	 * @return mixed
	 */
	abstract public function filter($value);

}
