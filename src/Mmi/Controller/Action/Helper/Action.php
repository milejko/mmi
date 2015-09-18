<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
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
		if (!$this->_checkAcl($controllerRequest->getModuleName(), $controllerRequest->getControllerName(), $controllerRequest->getActionName())) {
			\Mmi\Profiler::event('Action blocked: ' . $actionLabel);
			return;
		}
		//ustawienie requestu w widoku
		\Mmi\Controller\Front::getInstance()->getView()->setRequest($controllerRequest);
		//powołanie kontrolera
		$controllerParts = explode('-', $controllerRequest->getControllerName());
		foreach ($controllerParts as $key => $controllerPart) {
			$controllerParts[$key] = ucfirst($controllerPart);
		}
		$controllerClassName = ucfirst($controllerRequest->getModuleName()) . '\\Controller\\' . implode('\\', $controllerParts);
		$actionMethodName = $controllerRequest->getActionName() . 'Action';
		$controller = new $controllerClassName($controllerRequest);
		//wywołanie akcji
		$directContent = $controller->$actionMethodName();
		\Mmi\Profiler::event('Action executed: ' . $actionLabel);
		//jeśli akcja zwraca cokolwiek, automatycznie jest to content
		if ($directContent !== null) {
			\Mmi\Controller\Front::getInstance()->getView()
				->setLayoutDisabled()
				->setRequest($frontRequest);
			return $directContent;
		}
		//rendering szablonu jeśli akcja zwraca null
		$content = \Mmi\Controller\Front::getInstance()->getView()->renderTemplate($controllerRequest->getModuleName(), $controllerRequest->getControllerName(), $controllerRequest->getActionName());
		//przywrócenie do widoku request'a z front controllera
		\Mmi\Controller\Front::getInstance()->getView()->setRequest($frontRequest);
		return $content;
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
