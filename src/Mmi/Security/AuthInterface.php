<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

interface AuthInterface
{

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
