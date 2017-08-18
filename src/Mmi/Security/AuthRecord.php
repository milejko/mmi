<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Security;

/**
 * Klasa rekordu autoryzacji
 */
class AuthRecord
{

    /**
     * Identyfikator użytkownika
     * @var mixed
     */
    public $id;

    /**
     * Nazwa użytkownika
     * @var string
     */
    public $username;

    /**
     * Email
     * @var string
     */
    public $email;

    /**
     * Pełna nazwa użytkownika
     * @var string
     */
    public $name;

    /**
     * Role
     * @var array
     */
    public $roles = ['guest'];

    /**
     * Język
     * @var string
     */
    public $lang;

    /**
     * Dodatkowe dane
     * @var mixed
     */
    public $data;

}
