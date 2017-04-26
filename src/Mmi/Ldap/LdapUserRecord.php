<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Ldap;

/**
 * Rekord użytkownika LDAP
 */
class LdapUserRecord
{

    /**
     * Distinguished Name
     * @var string
     */
    public $dn;

    /**
     * Nazwa wspólna (commonName)
     * @var string
     */
    public $cn;

    /**
     * Login użytkownika
     * @var string
     */
    public $sAMAccountname;

    /**
     * Imię
     * @var string 
     */
    public $givenName;

    /**
     * Nazwisko (surname)
     * @var string
     */
    public $sn;

    /**
     * Mail
     * @var string
     */
    public $mail;

    /**
     * Lista grup
     * @var array
     */
    public $memberOf = [];

    /**
     * Identyfikator użytkownika (uid)
     * @var string
     */
    public $uid;

}
