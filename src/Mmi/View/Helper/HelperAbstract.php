<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\View\Helper;

class HelperAbstract {

	/**
	 * Referencja do widoku
	 * @var \Mmi\View
	 */
	public $view;

	/**
	 * Metoda programisty końcowego, wykonuje się na końcu konstruktora
	 */
	public function init() {
		
	}

	/**
	 * Konstruktor, ustawia widok
	 */
	public function __construct() {
		$this->view = \Mmi\Controller\Front::getInstance()->getView();
		$this->init();
	}

}
