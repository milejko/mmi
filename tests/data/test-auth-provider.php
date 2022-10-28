<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 *
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Test;

use Mmi\Security\AuthRecord;

/**
 * Testowy auth provider
 */
class TestAuthProvider implements \Mmi\Security\AuthProviderInterface
{
    public function authenticate($identity, $credential): ?AuthRecord
    {
        //no identity
        if (!$identity) {
            return null;
        }
        if ($identity != $credential) {
            return null;
        }
        $ar = new AuthRecord;
        $ar->id = 1;
        $ar->email = 'test@example.com';
        $ar->username = 'test';
        $ar->roles = ['member'];
        return $ar;
    }

    public function idAuthenticate($identity): ?AuthRecord
    {
        if (!intval($identity)) {
            return null;
        }
        $ar = new AuthRecord;
        $ar->id = 1;
        $ar->email = 'test@example.com';
        $ar->username = 'test';
        $ar->roles = ['member'];
        return $ar;
    }

    public function deauthenticate(): void
    {
    }
}
