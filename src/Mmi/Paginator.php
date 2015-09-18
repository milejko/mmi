<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

class Paginator extends Paginator\Base\Renderer {

	/**
	 * Konstruktor, przyjmuje opcje, ustawia wartości domyślne
	 * @param array $options opcje
	 */
	public function __construct(array $options = []) {
		$this->setRowsPerPage(10)
			->setShowPages(10)
			->setPreviousLabel('&#171;')
			->setNextLabel('&#187;')
			->setHashHref('')
			->setPageVariable('p')
			->setOptions($options);
	}

}
