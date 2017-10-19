<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Test\Model;

/**
 * Testowy auth model
 */
class AuthModel implements \Mmi\Security\AuthInterface
{

    public static function authenticate($identity, $credential)
    {
        if ('fake' == $identity) {
            return 'not-a-security-record';
        }
        if ($identity != $credential) {
            return;
        }
        $ar = new \Mmi\Security\AuthRecord;
        $ar->id = 1;
        $ar->email = 'test@example.com';
        $ar->username = 'test';
        $ar->roles = ['member'];
        return $ar;
    }

    public static function idAuthenticate($identity)
    {
        if ('fake' == $identity) {
            return 'not-a-security-record';
        }
        if (!intval($identity)) {
            return;
        }
        $ar = new \Mmi\Security\AuthRecord;
        $ar->id = 1;
        $ar->email = 'test@example.com';
        $ar->username = 'test';
        $ar->roles = ['member'];
        return $ar;
    }

    public static function deauthenticate()
    {
        
    }

}
