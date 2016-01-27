<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Convert;

/**
 * Klasa konwersji tablic do ciągów znaków
 */
class Table {

	/**
	 * Kalkuluje hash tabeli
	 * @param array $table tabela
	 * @return string
	 */
	private static function _calculateTableHash(array $table) {
		return md5(print_r($table, true));
	}

	/**
	 * Koduje tabelę i zaszywa sumę kontrolną
	 * @param array $table tabela
	 * @return string
	 */
	public static function toString(array $table) {
		$table['_hash'] = self::_calculateTableHash($table);
		return base64_encode(serialize($table));
	}

	/**
	 * Dekoduje tabelę i sprawdza integralność danych
	 * @param string $hashedTable
	 * @return array
	 */
	public static function fromString($hashedTable) {
		$table = unserialize(base64_decode($hashedTable));
		if (!is_array($table)) {
			return false;
		}
		if (!isset($table['_hash'])) {
			return false;
		}
		$targetHash = $table['_hash'];
		unset($table['_hash']);
		if (self::_calculateTableHash($table) != $targetHash) {
			return false;
		}
		return $table;
	}

}
