<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Auth;

interface AuthInterface {

	/**
	 * Autoryzacja z podaniem identyfikatora i hasła
	 * @param string $identity identyfikator
	 * @param string $credential hasło
	 */
	public static function authenticate($identity, $credential);

	/**
	 * Zaufana autoryzacja z podaniem identyfikatora
	 * @param string $identity identyfikator
	 */
	public static function idAuthenticate($identity);

	/**
	 * Niszczy autoryzację
	 */
	public static function deauthenticate();
}
