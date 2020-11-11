<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

interface AuthProviderInterface
{

    /**
     * Autoryzacja z podaniem identyfikatora i hasła
     */
    public function authenticate(string $identity, string $credential): ?AuthRecord;

    /**
     * Zaufana autoryzacja z podaniem identyfikatora
     */
    public function idAuthenticate(string $identity): ?AuthRecord;

    /**
     * Niszczy autoryzację
     */
    public function deauthenticate(): void;
}
