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
class ActionHelper
{

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
    public static function getInstance()
    {
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
    public function setAcl(\Mmi\Security\Acl $acl)
    {
        $this->_acl = $acl;
        return $this;
    }

    /**
     * Ustawia obiekt autoryzacji
     * @param \Mmi\Security\Auth $auth
     * @return \Mmi\Security\Auth
     */
    public function setAuth(\Mmi\Security\Auth $auth)
    {
        $this->_auth = $auth;
        return $this;
    }

    /**
     * Uruchamia akcję z kontrolera ze sprawdzeniem ACL
     * @param array $params parametry
     * @return mixed
     */
    public function action(array $params = [])
    {
        $originalRequest = FrontController::getInstance()->getView()->request ? FrontController::getInstance()->getView()->request : new Request;
        //ustawienie nowego requestu
        $request = new Request($params);
        //sprawdzenie ACL
        if (!$this->_checkAcl($request)) {
            //logowanie zablokowania akcji
            FrontController::getInstance()->getProfiler()->event('Mvc\ActionExecuter: ' . $request->getAsColonSeparatedString() . ' blocked');
            return;
        }
        //wywołanie akcji
        if (null !== $actionContent = $this->_invoke($request)) {
            //reset requestu i wyłączenie layoutu
            FrontController::getInstance()->getView()->setRequest($originalRequest);
            return $actionContent;
        }
        //rendering szablonu jeśli akcja zwraca null
        $actionContent = FrontController::getInstance()->getView()->renderTemplate($request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName());
        //reset requestu
        FrontController::getInstance()->getView()->setRequest($originalRequest);
        return $actionContent;
    }

    /**
     * Przekierowuje wykonanie do akcji
     * @param \Mmi\Http\Request $request
     * @return string
     * @throws \Mmi\Mvc\MvcException
     */
    public function forward(Request $request)
    {
        //reset requesta frontcontrollera
        FrontController::getInstance()
            ->setRequest($request)
            ->getView()->setRequest($request);
        //sprawdzenie ACL
        if (!$this->_checkAcl($request)) {
            //wyjątek niedozwolonej akcji
            throw new MvcForbiddenException('Action ' . $request->getAsColonSeparatedString() . ' blocked');
        }
        //wywołanie akcji
        if (null !== $actionContent = $this->_invoke($request)) {
            //zwrot jeśli akcja zwraca wynik
            return $actionContent;
        }
        //renderowanie szablonu
        $actionContent = FrontController::getInstance()->getView()->renderTemplate($request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName());
        //jeśli layout jest wyłączony - zwrot szablonu, jeśli nie - layoutu
        return FrontController::getInstance()->getView()->isLayoutDisabled() ? $actionContent : FrontController::getInstance()->getView()
                ->setPlaceholder('content', $actionContent)
                ->renderTemplate($this->_getLayout($request));
    }

    /**
     * Sprawdza uprawnienie do widgetu
     * @param \Mmi\Http\Request $request
     * @return boolean
     */
    private function _checkAcl(Request $request)
    {
        //jeśli brak - dozwolone
        if ($this->_acl === null || $this->_auth === null) {
            return true;
        }
        //sprawdzenie acl
        return $this->_acl->isAllowed($this->_auth->getRoles(), $request->getAsColonSeparatedString());
    }

    /**
     * Wywołanie akcji
     * @param \Mmi\Http\Request $request
     * @return string
     * @throws MvcNotFoundException
     */
    private function _invoke(Request $request)
    {
        //informacja do profilera o rozpoczęciu wykonywania akcji
        FrontController::getInstance()->getProfiler()->event('Mvc\ActionExecuter: ' . $request->getAsColonSeparatedString() . ' start');
        //pobranie struktury
        $structure = FrontController::getInstance()->getStructure('module');
        //brak w strukturze
        if (!isset($structure[$request->getModuleName()][$request->getControllerName()][$request->getActionName()])) {
            throw new MvcNotFoundException('Component not found: ' . $request->getAsColonSeparatedString());
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
        //informacja o zakończeniu wykonywania akcji do profilera
        FrontController::getInstance()->getProfiler()->event('Mvc\ActionExecuter: ' . $request->getAsColonSeparatedString() . ' done');
        return $content;
    }

    /**
     * Pobiera dostępny layout
     * @param \Mmi\Http\Request $request
     * @return string
     * @throws \Mmi\Mvc\MvcException brak layoutów
     */
    private function _getLayout(\Mmi\Http\Request $request)
    {
        //test layoutu dla modułu i kontrolera
        if (null !== FrontController::getInstance()->getView()->getTemplateByPath($request->getModuleName() . '/' . $request->getControllerName() . '/layout')) {
            return $request->getModuleName() . '/' . $request->getControllerName() . '/layout';
        }
        //test layoutu dla modułu
        if (null !== FrontController::getInstance()->getView()->getTemplateByPath($request->getModuleName() . '/layout')) {
            return $request->getModuleName() . '/layout';
        }
        //test layoutu dla modułu
        if (null !== FrontController::getInstance()->getView()->getTemplateByPath('app/layout')) {
            return 'app/layout';
        }
        //brak layoutu
        throw new \Mmi\Mvc\MvcException('Layout not found.');
    }

}
