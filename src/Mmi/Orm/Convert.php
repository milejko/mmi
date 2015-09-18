<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Orm;

/**
 * Klasa konwersji
 */
class Convert {

	/**
	 * Konwertuje podkreślenia na camelcase
	 * @param string $value
	 * @return string
	 */
	public static final function underscoreToCamelcase($value) {
		//używa callbacku
		return preg_replace_callback('/\_([a-z0-9])/', function ($matches) {
			return ucfirst($matches[1]);
		}, $value);
	}

	/**
	 * Konwertuje camelcase na podkreślenia
	 * @param string $value
	 * @return string
	 */
	public static final function camelcaseToUnderscore($value) {
		//używa callbacku
		return preg_replace_callback('/([A-Z])/', function ($matches) {
			return '_' . lcfirst($matches[1]);
		}, $value);
	}

}
