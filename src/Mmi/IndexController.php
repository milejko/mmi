<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

class IndexController extends Mvc\Controller {

	public function indexAction() {
		$grid = new \CmsAdmin\Grid\Grid();
		$grid->setQuery(\Cms\Orm\CmsLogQuery::factory());
		$grid->getState()
			->setFilters(['operation|equals' => 'login'])
			->setOrder(['dateTime' => 'asc'])
			->setRowsPerPage(10)
			->setPage(1);
		$grid->render();
		exit;
	}

	public function errorAction() {
		
	}

}
