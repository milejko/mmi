<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Controller\Action\Helper;

class Action {

	/**
	 * Obiekt ACL
	 * @var \Mmi\Acl
	 */
	protected $_acl;

	/**
	 * Obiekt Auth
	 * @var \Mmi\Auth
	 */
	protected $_auth;
	
	/**
	 * Instancja helpera akcji
	 * @var \Mmi\Controller\Action\Helper\Action 
	 */
	protected static $_instance;

	/**
	 * Pobranie instancji
	 * @return \Mmi\Controller\Action\Helper\Action
	 */
	public static function getInstance() {
		//jeśli nie istnieje instancja tworzenie nowej
		if (null === self::$_instance) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Ustawia obiekt ACL
	 * @param \Mmi\Acl $acl
	 * @return \Mmi\Acl
	 */
	public function setAcl(\Mmi\Acl $acl) {
		$this->_acl = $acl;
		return $this;
	}

	/**
	 * Ustawia obiekt autoryzacji
	 * @param \Mmi\Auth $auth
	 * @return \Mmi\Auth
	 */
	public function setAuth(\Mmi\Auth $auth) {
		$this->_auth = $auth;
		return $this;
	}

	/**
	 * Uruchamia akcję z kontrolera
	 * @param array $params parametry
	 * @return mixed
	 */
	public function action(array $params = []) {
		$frontRequest = \Mmi\Controller\Front::getInstance()->getRequest();
		$controllerRequest = new \Mmi\Controller\Request(array_merge($frontRequest->toArray(), $params));
		$actionLabel = $controllerRequest->getModuleName() . ':' . $controllerRequest->getControllerName() . ':' . $controllerRequest->getActionName();
		//sprawdzenie ACL
		if (!$this->_checkAcl($controllerRequest->getModuleName(), $controllerRequest->getControllerName(), $controllerRequest->getActionName())) {
			\Mmi\Profiler::event('Action blocked: ' . $actionLabel);
			return;
		}
		//wywołanie akcji
		$actionContent = $this->_invokeAction($controllerRequest, $actionLabel);
		\Mmi\Profiler::event('Action executed: ' . $actionLabel);
		//jeśli akcja zwraca cokolwiek, automatycznie jest to content
		if ($actionContent !== null) {
			\Mmi\Controller\Front::getInstance()->getView()
				->setLayoutDisabled()
				->setRequest($frontRequest);
			return $actionContent;
		}
		//rendering szablonu jeśli akcja zwraca null
		$content = \Mmi\Controller\Front::getInstance()->getView()->renderTemplate($controllerRequest->getModuleName(), $controllerRequest->getControllerName(), $controllerRequest->getActionName());
		//przywrócenie do widoku request'a z front controllera
		\Mmi\Controller\Front::getInstance()->getView()->setRequest($frontRequest);
		return $content;
	}
	
	/**
	 * Wykonuje akcję
	 * @param \Mmi\Controller\Request $request
	 * @param string $actionLabel
	 * @return string
	 * @throws \Exception
	 */
	protected function _invokeAction(\Mmi\Controller\Request $request, $actionLabel) {
		$structure = \Mmi\Controller\Front::getInstance()->getStructure('module');
		//brak w strukturze
		if (!isset($structure[$request->getModuleName()][$request->getControllerName()][$request->getActionName()])) {
			throw new \Exception('Action not found: ' . $actionLabel);
		}
		//ustawienie requestu w widoku
		\Mmi\Controller\Front::getInstance()->getView()->setRequest($request);
		//powołanie kontrolera
		$controllerParts = explode('-', $request->getControllerName());
		foreach ($controllerParts as $key => $controllerPart) {
			$controllerParts[$key] = ucfirst($controllerPart);
		}
		$controllerClassName = ucfirst($request->getModuleName()) . '\\Controller\\' . implode('\\', $controllerParts);
		$actionMethodName = $request->getActionName() . 'Action';
		$controller = new $controllerClassName($request);
		//wywołanie akcji
		return $controller->$actionMethodName();
	}

	/**
	 * Sprawdza uprawnienie do widgetu
	 * @param string $module moduł
	 * @param string $controller kontroler
	 * @param string $action akcja
	 * @return boolean
	 */
	protected function _checkAcl($module, $controller, $action) {
		//jeśli brak - dozwolone
		if ($this->_acl === null || $this->_auth === null) {
			return true;
		}
		return $this->_acl->isAllowed($this->_auth->getRoles(), $module . ':' . $controller . ':' . $action);
	}

}
