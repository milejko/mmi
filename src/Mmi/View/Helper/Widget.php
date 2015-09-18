<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\View\Helper;

class Widget extends HelperAbstract {

	/**
	 * Metoda główna, renderuje widget o zadanych parametrach
	 * @param string $module moduł
	 * @param string $controller kontroler
	 * @param string $action akcja
	 * @param array $params parametry
	 * @return string
	 */
	public function widget($module, $controller = 'index', $action = 'index', array $params = []) {
		$isLayoutDisabled = $this->view->isLayoutDisabled();
		$params['module'] = $module;
		$params['controller'] = $controller;
		$params['action'] = $action;
		$actionResult = \Mmi\Controller\Action\Helper\Action::getInstance()->action($params);
		$this->view->setLayoutDisabled($isLayoutDisabled);
		return $actionResult;
	}

}
