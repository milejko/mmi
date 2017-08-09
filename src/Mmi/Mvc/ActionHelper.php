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
        //zwrot instancji, lub utworzenie nowej
        return self::$_instance ? self::$_instance : (self::$_instance = new self);
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
     * @param \Mmi\Http\Request $request
     * @return mixed
     */
    public function action(Request $request, $main = false)
    {
        //sprawdzenie ACL
        if (!$this->_checkAcl($request)) {
            //logowanie zablokowania akcji
            FrontController::getInstance()->getProfiler()->event('Mvc\ActionExecuter: ' . $request->getAsColonSeparatedString() . ' blocked');
            return;
        }
        //rendering szablonu jeśli akcja zwraca null
        return $this->_renderAction($request, (FrontController::getInstance()->getView()->request ? FrontController::getInstance()->getView()->request : new Request), $main);
    }

    /**
     * Przekierowuje wykonanie do akcji
     * @param \Mmi\Http\Request $request
     * @return string
     * @throws \Mmi\Mvc\MvcException
     */
    public function forward(Request $request)
    {
        //sprawdzenie ACL
        if (!$this->_checkAcl($request)) {
            //wyjątek niedozwolonej akcji
            throw new MvcForbiddenException('Action ' . $request->getAsColonSeparatedString() . ' blocked');
        }
        //zmiana requestu front-controllera
        FrontController::getInstance()->setRequest($request);
        //render layoutu
        return $this->layout($this->_renderAction($request, $request, true), $request);
    }

    /**
     * Umieszcza content w layoucie
     * @param string $content
     * @param Request $request
     * @return string
     */
    public function layout($content, Request $request)
    {
        //jeśli layout jest wyłączony - zwrot szablonu, jeśli nie - layoutu
        return FrontController::getInstance()->getView()->isLayoutDisabled() ? $content : FrontController::getInstance()->getView()
                ->setPlaceholder('content', $content)
                ->renderTemplate($this->_getLayout($request));
    }

    /**
     * Renderuje akcję (zwraca content akcji, lub template)
     * @param Request $request
     * @param Request $resetRequest request przekazywany do widoku po zakończeniu renderingu
     * @param boolean $main określa czy akcja jest akcją główną (2 przypadki - gdy wywołana z front-controllera, lub forward)
     * @return string
     */
    private function _renderAction(Request $request, Request $resetRequest, $main)
    {
        $resetLayoutDisabled = FrontController::getInstance()->getView()->isLayoutDisabled();
        //wywołanie akcji
        if (null !== $actionContent = $this->_invokeAction($request)) {
            //reset requestu i dla akcji głównej wyłączenie layoutu
            FrontController::getInstance()->getView()->setRequest($resetRequest)
                ->setLayoutDisabled($main ? true : FrontController::getInstance()->getView()->isLayoutDisabled());
            //zwrot jeśli akcja zwraca wynik
            return $actionContent;
        }
        //zwrot wyrenderowanego szablonu
        $content = FrontController::getInstance()->getView()->renderTemplate($request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName());
        //reset requestu i przywracanie layoutu (jeśli nie jest akcją główną)
        FrontController::getInstance()->getView()
            ->setRequest($resetRequest)
            ->setLayoutDisabled($main ? FrontController::getInstance()->getView()->isLayoutDisabled() : $resetLayoutDisabled);
        FrontController::getInstance()->getProfiler()->event('Mvc\View: ' . $request->getAsColonSeparatedString() . ' rendered');
        return $content;
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
    private function _invokeAction(Request $request)
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
