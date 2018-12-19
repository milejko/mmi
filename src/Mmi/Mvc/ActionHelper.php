<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\App\FrontController;
use Mmi\Http\Request;

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
            return FrontController::getInstance()->getProfiler()->event('Mvc\ActionExecuter: ' . $request->getAsColonSeparatedString() . ' blocked');
        }
        //rendering szablonu jeśli akcja zwraca null
        return $this->_renderAction($request, (FrontController::getInstance()->getView()->request ? FrontController::getInstance()->getView()->request : new Request), $main);
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
        //iteracja po pluginach front controllera
        foreach (FrontController::getInstance()->getPlugins() as $plugin) {
            //post dispatch
            $plugin->postDispatch($request);
            FrontController::getInstance()->getProfiler()->event('Mvc\ActionHelper: plugins post-dispatch');
        }
        //zmiana requestu i render layoutu
        return FrontController::getInstance()
            ->setRequest($request)
            ->getView()->renderLayout($content, $request);
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
        //zapamiętanie stanu wyłączenia layoutu
        $resetLayoutDisabled = FrontController::getInstance()->getView()->isLayoutDisabled();
        //ustawienia requestu
        FrontController::getInstance()->getView()->setRequest($request);
        //wywołanie akcji
        if (null !== $actionContent = $this->_invokeAction($request)) {
            //reset requestu i dla akcji głównej wyłączenie layoutu
            FrontController::getInstance()->getView()
                ->setRequest($resetRequest)
                //jeśli akcja główna - to ona decyduje o wyłączeniu layoutu, jeśli nie - reset do tego co było przed nią
                ->setLayoutDisabled($main ? true : FrontController::getInstance()->getView()->isLayoutDisabled());
            //zwrot danych z akcji
            return $actionContent;
        }
        //zwrot wyrenderowanego szablonu
        $content = FrontController::getInstance()->getView()->renderTemplate($request->getModuleName() . '/' . $request->getControllerName() . '/' . $request->getActionName());
        //pobranie widoku
        FrontController::getInstance()->getView()
            ->setRequest($resetRequest)
            //jeśli akcja główna - to ona decyduje o wyłączeniu layoutu, jeśli nie - reset do tego co było przed nią
            ->setLayoutDisabled($main ? FrontController::getInstance()->getView()->isLayoutDisabled() : $resetLayoutDisabled);
        //profiler wyrenderowaniu szablonu
        FrontController::getInstance()->getProfiler()->event('Mvc\View: ' . $request->getAsColonSeparatedString() . ' rendered');
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
        FrontController::getInstance()->getProfiler()->event('Mvc\ActionHelper: ' . $request->getAsColonSeparatedString() . ' start');
        //pobranie struktury
        $structure = FrontController::getInstance()->getStructure('module');
        //sprawdzenie w strukturze
        if (!isset($structure[$request->getModuleName()][$request->getControllerName()][$request->getActionName()])) {
            //komponent nieodnaleziony
            throw new MvcNotFoundException('Component not found: ' . $request->getAsColonSeparatedString());
        }
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

        //inicjalizacja tłumaczeń
        $this->_initTranslaction(FrontController::getInstance()->getView(), $request->module, $request->lang);

        //wywołanie akcji
        $content = (new $controllerClassName($request, FrontController::getInstance()->getView()))->$actionMethodName();
        //informacja o zakończeniu wykonywania akcji do profilera
        FrontController::getInstance()->getProfiler()->event('Mvc\ActionHelper: ' . $request->getAsColonSeparatedString() . ' done');
        return $content;
    }

    /**
     * Inicjalizacja tłumaczeń
     * @param \Mmi\Mvc\View $view
     * @param string $module nazwa modułu
     * @param string $lang język
     * @return mixed wartość
     */
    private function _initTranslaction(\Mmi\Mvc\View $view, $module, $lang)
    {
        //pobranie struktury translatora
        $structure = FrontController::getInstance()->getStructure('translate');
        //brak tłumaczenia w strukturze
        if (!isset($structure[$module][$lang])) {
            return;
        }
        //brak tłumaczenia, lub domyślny język
        if ($lang === null || $lang == \App\Registry::$translate->getDefaultLocale()) {
            return;
        }
        //ładowanie zbuforowanego translatora
        $cache = $view->getCache();
        //klucz buforowania
        $key = 'mmi-translate-' . $lang . '-' . '-' . $module;
        //próba załadowania z bufora
        if ($cache !== null && (null !== ($cachedTranslate = $cache->load($key)))) {
            //zmiana obiektu translacji z bufora
            \App\Registry::$translate = $cachedTranslate->setLocale($lang);
            return FrontController::getInstance()->getProfiler()->event('Mvc\Controller: translate cache [' . $lang . '] ' . $module);
        }
        //dodawanie tłumaczeń do translatora
        \App\Registry::$translate->addTranslation(is_array($structure[$module][$lang]) ? $structure[$module][$lang][0] : $structure[$module][$lang], $lang)
            ->setLocale($lang);
        //zapis do cache
        if ($cache !== null) {
            $cache->save(\App\Registry::$translate, $key, 0);
        }
        //event profilera
        FrontController::getInstance()->getProfiler()->event('Mvc\Controller: translate cache [' . $lang . '] ' . $module);
    }

}
