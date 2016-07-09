<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc;

use Mmi\App\FrontController,
	\Mmi\Http\Request;

/**
 * Helper akcji
 */
class ActionHelper {

	/**
	 * Obiekt ACL
	 * @var \Mmi\Security\Acl
	 */
	protected $_acl;

	/**
	 * Obiekt Auth
	 * @var \Mmi\Security\Auth
	 */
	protected $_auth;

	/**
	 * Instancja helpera akcji
	 * @var \Mmi\Mvc\ActionHelper 
	 */
	protected static $_instance;

	/**
	 * Pobranie instancji
	 * @return \Mmi\Mvc\ActionHelper
	 */
	public static function getInstance() {
		//jeśli nie istnieje instancja tworzenie nowej
		if (null === self::$_instance) {
			self::$_instance = new self;
		}
		return self::$_instance;
	}

	/**
	 * Ustawia obiekt ACL
	 * @param \Mmi\Security\Acl $acl
	 * @return \Mmi\Security\Acl
	 */
	public function setAcl(\Mmi\Security\Acl $acl) {
		$this->_acl = $acl;
		return $this;
	}

	/**
	 * Ustawia obiekt autoryzacji
	 * @param \Mmi\Security\Auth $auth
	 * @return \Mmi\Security\Auth
	 */
	public function setAuth(\Mmi\Security\Auth $auth) {
		$this->_auth = $auth;
		return $this;
	}

	/**
	 * Uruchamia akcję z kontrolera ze sprawdzeniem ACL
	 * @param array $params parametry
	 * @return mixed
	 */
	public function action(array $params = []) {
		//ustawienie nowego requestu
		$request = new Request(array_merge(FrontController::getInstance()->getRequest()->toArray(), $params));
		//wywołanie akcji
		if (null !== $actionContent = $this->_invoke($request)) {
			//reset requestu i wyłączenie layoutu
			FrontController::getInstance()->getView()->setRequest(FrontController::getInstance()->getRequest())
				->setLayoutDisabled();
			return $actionContent;
		}
		//rendering szablonu jeśli akcja zwraca null
		$content = FrontController::getInstance()->getView()->renderTemplate($request);
		//reset requestu
		FrontController::getInstance()->getView()->setRequest(FrontController::getInstance()->getRequest());
		return $content;
	}

	/**
	 * Przekierowuje wykonanie do akcji
	 * @param \Mmi\Http\Request $request
	 * @return string
	 * @throws \Mmi\Mvc\MvcException
	 */
	public function forward(Request $request) {
		//reset requesta frontcontrollera
		FrontController::getInstance()
			->setRequest($request)
			->getView()->setRequest($request);
		//wywołanie akcji
		if (null !== $actionContent = $this->_invoke($request)) {
			//wyłączenie layout
			FrontController::getInstance()->getView()->setLayoutDisabled();
			//zwrot jeśli akcja zwraca wynik
			return $actionContent;
		}
		return FrontController::getInstance()->getView()
				->setPlaceholder('content', FrontController::getInstance()->getView()->renderTemplate($request))
				->renderLayout($request);
	}

	/**
	 * Sprawdza uprawnienie do widgetu
	 * @param string $module moduł
	 * @param string $controller kontroler
	 * @param string $action akcja
	 * @return boolean
	 */
	private function _checkAcl($module, $controller, $action) {
		//jeśli brak - dozwolone
		if ($this->_acl === null || $this->_auth === null) {
			return true;
		}
		//sprawdzenie acl
		return $this->_acl->isAllowed($this->_auth->getRoles(), $module . ':' . $controller . ':' . $action);
	}

	/**
	 * Wywołanie akcji
	 * @param \Mmi\Http\Request $request
	 * @return string
	 * @throws MvcNotFoundException
	 */
	private function _invoke(Request $request) {
		//labelka akcji
		$actionLabel = $request->getModuleName() . ':' . $request->getControllerName() . ':' . $request->getActionName();
		//sprawdzenie ACL
		if (!$this->_checkAcl($request->getModuleName(), $request->getControllerName(), $request->getActionName())) {
			FrontController::getInstance()->getProfiler()->event('Mvc\ActionExecuter: ' . $actionLabel . ' blocked');
			return;
		}
		//pobranie struktury
		$structure = FrontController::getInstance()->getStructure('module');
		//brak w strukturze
		if (!isset($structure[$request->getModuleName()][$request->getControllerName()][$request->getActionName()])) {
			throw new MvcNotFoundException('Component not found: ' . $actionLabel);
		}
		//ustawienie requestu w widoku
		FrontController::getInstance()->getView()->setRequest($request);
		//powołanie kontrolera
		$controllerParts = explode('-', $request->getControllerName());
		foreach ($controllerParts as $key => $controllerPart) {
			$controllerParts[$key] = ucfirst($controllerPart);
		}
		//ustalenie klasy kontrolera
		$controllerClassName = ucfirst($request->getModuleName()) . '\\' . implode('\\', $controllerParts) . 'Controller';
		$actionMethodName = $request->getActionName() . 'Action';
		//wywołanie akcji
		$content = (new $controllerClassName($request))->$actionMethodName();
		FrontController::getInstance()->getProfiler()->event('Mvc\ActionExecuter: ' . $actionLabel . ' done');
		return $content;
	}

}
