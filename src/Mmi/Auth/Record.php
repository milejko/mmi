<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Auth;

/**
 * Klasa rekordu autoryzacji
 */
class Record {
	
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
