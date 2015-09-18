<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Controller\Request;

class Post extends \Mmi\DataObject {
	
	/**
	 * Konstruktor
	 * @param array $post dane z POST
	 */
	public function __construct(array $post = []) {
		$this->_data = $post;
	}
	
}