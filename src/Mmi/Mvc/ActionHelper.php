<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\App\AppEventInterceptorInterface;
use Mmi\App\AppProfilerInterface;
use Mmi\Http\Request;
use Psr\Container\ContainerInterface;

/**
 * Helper akcji
 */
class ActionHelper
{
    public const PROFILER_ACTION_PREFIX = 'Mmi\Mvc\Controller: ';
    public const PROFILER_TEMPLATE_PREFIX = 'Mmi\Mvc\View: ';
    public const PROFILER_PREFIX = 'Mmi\Mvc\ActionHelper: ';

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
     * @var AppProfilerInterface
     */
    private $profiler;

    /**
     * @var View
     */
    private $view;

    /**
     * @var AppEventInterceptorAbstract
     */
    private $appEventInterceptor;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Pobranie instancji
     * @return \Mmi\Mvc\ActionHelper
     */
    public function __construct(ContainerInterface $container)
    {
        $this->profiler             = $container->get(AppProfilerInterface::class);
        $this->view                 = $container->get(View::class);
        $this->appEventInterceptor  = $container->has(AppEventInterceptorInterface::class) ? $container->get(AppEventInterceptorInterface::class) : null;
        $this->container            = $container;
    }

    /**
     * Ustawia obiekt ACL
     * @param \Mmi\Security\Acl $acl
     * @return \Mmi\Security\Acl
     */
    public function setAcl(\Mmi\Security\Acl $acl)
    {
        //acl
        $this->_acl = $acl;
        //zwrot siebie
        return $this;
    }

    /**
     * Ustawia obiekt autoryzacji
     * @param \Mmi\Security\Auth $auth
     * @return \Mmi\Security\Auth
     */
    public function setAuth(\Mmi\Security\Auth $auth)
    {
        //auth
        $this->_auth = $auth;
        //zwrot siebie
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
            return $this->profiler->event(self::PROFILER_ACTION_PREFIX . $request->getAsColonSeparatedString() . ' blocked');
        }
        //rendering szablonu jeśli akcja zwraca null
        return $this->_renderAction($request, ($this->view->request ? $this->view->request : new Request()), $main);
    }

    /**
     * Przekierowuje na request zwraca wyrenderowaną akcję i layout
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
        //renderowanie akcji
        $content = $this->_renderAction($request, $request, true);
        //intercept afterDispatch
        if ($this->appEventInterceptor) {
            $this->appEventInterceptor->afterDispatch();
            $this->profiler->event(self::PROFILER_PREFIX . 'interceptor afterDispatch()');
        }
        //renderowanie layoutu
        $rendered = $this->view->renderLayout($content, $request);
        $this->profiler->event(self::PROFILER_PREFIX . 'layout rendered');
        return $rendered;
    }

    /**
     * Renderuje akcję (zwraca content akcji, lub template)
     * @param Request $request
     * @param Request $resetRequest request przekazywany do widoku po zakończeniu renderingu
     * @param boolean $main określa czy akcja jest akcją główną (2 przypadki - wywoływana z app, lub forward)
     * @return string
     */
    private function _renderAction(Request $request, Request $resetRequest, $main)
    {
        //zapamiętanie stanu wyłączenia layoutu
        $resetLayoutDisabled = $this->view->isLayoutDisabled();
        //ustawienia requestu
        $this->view->setRequest($request);
        //wywołanie akcji
        if (null !== $actionContent = $this->_invokeAction($request)) {
            //reset requestu i dla akcji głównej wyłączenie layoutu
            $this->view
                ->setRequest($resetRequest)
                //jeśli akcja główna - to ona decyduje o wyłączeniu layoutu, jeśli nie - reset do tego co było przed nią
                ->setLayoutDisabled($main ? true : $this->view->isLayoutDisabled());
            //zwrot danych z akcji
            return $actionContent;
        }
        //zwrot wyrenderowanego szablonu
        $content = $this->view->renderTemplate($request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName());
        //pobranie widoku
        $this->view
            ->setRequest($resetRequest)
            //jeśli akcja główna - to ona decyduje o wyłączeniu layoutu, jeśli nie - reset do tego co było przed nią
            ->setLayoutDisabled($main ? $this->view->isLayoutDisabled() : $resetLayoutDisabled);
        //profiler wyrenderowaniu szablonu
        $this->profiler->event(self::PROFILER_TEMPLATE_PREFIX . $request->getAsColonSeparatedString() . ' rendered');
        //zwrot wyrenderowanego szablonu
        return $content;
    }

    /**
     * Sprawdza uprawnienie do widgetu
     * @param \Mmi\Http\Request $request
     * @return boolean
     */
    private function _checkAcl(Request $request)
    {
        //brak acl lub brak auth lub dozwolone acl
        return !$this->_acl || !$this->_auth || $this->_acl->isAllowed($this->_auth->getRoles(), $request->getAsColonSeparatedString());
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
        $this->profiler->event(self::PROFILER_ACTION_PREFIX . $request->getAsColonSeparatedString() . ' start');
        //rozbijanie po myślniku
        $controllerParts = explode('-', $request->getControllerName());
        //iteracja po częściach
        foreach ($controllerParts as $key => $controllerPart) {
            //stosowanie camelcase
            $controllerParts[$key] = ucfirst($controllerPart);
        }
        //ustalenie klasy kontrolera
        $controllerClassName = ucfirst($request->getModuleName()) . '\\' . implode('\\', $controllerParts) . 'Controller';
        //nazwa akcji
        $actionMethodName = $request->getActionName() . 'Action';
        //check for controller existence
        if (!$this->container->has($controllerClassName) || !\method_exists($this->container->get($controllerClassName), $actionMethodName)) {
            throw new MvcNotFoundException('Component not found: ' . $request->getAsColonSeparatedString());
        }
        //wywołanie akcji
        $content = \call_user_func([$this->container->get($controllerClassName), $actionMethodName], $request);
        //informacja o zakończeniu wykonywania akcji do profilera
        $this->profiler->event(self::PROFILER_ACTION_PREFIX . $request->getAsColonSeparatedString() . ' done');
        return $content;
    }
}
