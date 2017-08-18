<?php

namespace App;

use \Mmi\App\FrontController;

/**
 * Plugin front kontrolera (hooki)
 */
class FrontControllerPlugin extends \Mmi\App\FrontControllerPluginAbstract
{

    /**
     * Przed uruchomieniem dispatchera
     * @param \Mmi\Http\Request $request
     */
    public function preDispatch(\Mmi\Http\Request $request)
    {
        //niepoprawny język
        if ($request->__get('lang') && !in_array($request->__get('lang'), \App\Registry::$config->languages)) {
            throw new \Mmi\Mvc\MvcNotFoundException('Language not found');
        }
        //konfiguracja autoryzacji
        $auth = new \Mmi\Security\Auth;
        $auth->setSalt(\App\Registry::$config->salt)
            ->setModelName(\App\Registry::$config->session->authModel ? \App\Registry::$config->session->authModel : '\Cms\Model\Auth');
        \App\Registry::$auth = $auth;
        \Mmi\Mvc\ActionHelper::getInstance()->setAuth($auth);
        \Mmi\Mvc\ViewHelper\Navigation::setAuth($auth);

        //funkcja pamiętaj mnie realizowana poprzez cookie
        $cookie = new \Mmi\Http\Cookie;
        $remember = \App\Registry::$config->session->authRemember ? \App\Registry::$config->session->authRemember : 0;
        if ($remember > 0 && !$auth->hasIdentity() && $cookie->match('remember')) {
            $params = [];
            parse_str($cookie->getValue(), $params);
            if (isset($params['id']) && isset($params['key']) && $params['key'] == md5(\App\Registry::$config->salt . $params['id'])) {
                $auth->setIdentity($params['id']);
                $auth->idAuthenticate();
                //regeneracja ID sesji po autoryzacji
                \Mmi\Session\Session::regenerateId();
            }
        }
        //autoryzacja do widoku
        if ($auth->hasIdentity()) {
            \Mmi\App\FrontController::getInstance()->getView()->auth = $auth;
        }

        //ustawienie acl
        if (null === ($acl = \App\Registry::$cache->load('mmi-cms-acl'))) {
            $acl = \Cms\Model\Acl::setupAcl();
            \App\Registry::$cache->save($acl, 'mmi-cms-acl', 0);
        }
        \Mmi\App\FrontController::getInstance()->getView()->acl = \App\Registry::$acl = $acl;
        \Mmi\Mvc\ActionHelper::getInstance()->setAcl($acl);
        \Mmi\Mvc\ViewHelper\Navigation::setAcl($acl);

        //ustawienie nawigatora
        if (null === ($navigation = \App\Registry::$cache->load('mmi-cms-navigation-' . $request->__get('lang')))) {
            (new \Cms\Model\Navigation)->decorateConfiguration(\App\Registry::$config->navigation);
            $navigation = new \Mmi\Navigation\Navigation(\App\Registry::$config->navigation);
            //zapis do cache
            \App\Registry::$cache->save($navigation, 'mmi-cms-navigation-' . $request->__get('lang'), 0);
        }
        $navigation->setup($request);
        //przypinanie nawigatora do helpera widoku nawigacji
        \Mmi\Mvc\ViewHelper\Navigation::setNavigation(\App\Registry::$navigation = $navigation);

        //zablokowane na ACL
        if ($acl->isAllowed($auth->getRoles(), $actionLabel = strtolower($request->getModuleName() . ':' . $request->getControllerName() . ':' . $request->getActionName()))) {
            return;
        }
        $moduleStructure = \Mmi\App\FrontController::getInstance()->getStructure('module');
        //brak w strukturze
        if (!isset($moduleStructure[$request->getModuleName()][$request->getControllerName()][$request->getActionName()])) {
            throw new \Mmi\Mvc\MvcNotFoundException('Component not found: ' . $actionLabel);
        }
        //brak autoryzacji i kontroler admina - przekierowanie na logowanie
        if (!$auth->hasIdentity()) {
            //logowanie admina
            return $this->_setLoginRequest($request, strpos($request->getModuleName(), 'Admin'));
        }
        \App\Registry::$auth->clearIdentity();
        //zalogowany na nieuprawnioną rolę
        throw new \Mmi\Mvc\MvcNotFoundException('Unauthorized access');
    }

    /**
     * Wykonywana po dispatcherze
     * @param \Mmi\Http\Request $request
     */
    public function postDispatch(\Mmi\Http\Request $request)
    {
        //ustawienie widoku
        $view = \Mmi\App\FrontController::getInstance()->getView();
        $base = $view->baseUrl;
        $view->domain = \App\Registry::$config->host;
        $view->languages = \App\Registry::$config->languages;
        $jsRequest = $request->toArray();
        $jsRequest['baseUrl'] = $base;
        unset($jsRequest['controller']);
        unset($jsRequest['action']);
        //umieszczenie tablicy w headScript()
        $view->headScript()->prependScript('var request = ' . json_encode($jsRequest));
        //jesli generator i wywolanie usuniecia zadania/wariantu nie wymuszamy http czy https
        if ($request->getModuleName() == 'cmsAdmin' && $request->getControllerName() == 'upload') {
            return;
        }
        //jeśli strona nie jest adminowa - wyjście z SSL
        if (strpos($request->getModuleName(), 'Admin') || $request->getModuleName() == 'user') {
            //wymuszenie ssl
            return $this->_forceSsl($request);
        }
        //wymuszenie braku ssl
        $this->_forceNonSsl($request);
    }
    
    /**
     * Zwraca http lub https
     * @return string
     */
    public function getProtocol()
    {
        if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            return 'https';
        } else {
            return 'http';
        }
    }

    /**
     * Ustawia request na logowanie admina
     * @param \Mmi\Http\Request $request
     */
    protected function _setLoginRequest(\Mmi\Http\Request $request, $preferAdmin)
    {
        //logowanie bez preferencji admina, tylko gdy uprawniony
        if (false === $preferAdmin && \App\Registry::$acl->isRoleAllowed('guest', 'user:login:index')) {
            return $request->setModuleName('user')
                    ->setControllerName('login')
                    ->setActionName('index');
        }
        //logowanie admina
        return $request->setModuleName('cmsAdmin')
                ->setControllerName('index')
                ->setActionName('login');
    }

    /**
     * Wymuszenie SSL
     * @param \Mmi\Http\Request $request
     */
    protected function _forceSsl(\Mmi\Http\Request $request)
    {
        //przekierowanie na https (cms)
        if (!FrontController::getInstance()->getEnvironment()->httpSecure) {
            FrontController::getInstance()->getResponse()->redirectToUrl(FrontController::getInstance()->getView()->url($request->toArray(), true, true));
        }
    }

    /**
     * Wymuszenie braku SSL
     * @param \Mmi\Http\Request $request
     */
    protected function _forceNonSsl(\Mmi\Http\Request $request)
    {
        //brak wymuszenia ssl
        if (!$this->_forceNonSslByRequest($request)) {
            return;
        }
        //strona główna
        if ($request->uri == '/') {
            $request = new \Mmi\Http\Request;
            $request->module = 'cms';
            $request->controller = 'category';
            $request->action = 'dispatch';
            $request->uri = '/';
        }
        //jeśli aplikacja w trybie SSL
        if (FrontController::getInstance()->getEnvironment()->httpSecure) {
            FrontController::getInstance()->getResponse()->redirectToUrl(FrontController::getInstance()->getView()->url($request->toArray(), true, false));
        }
    }

    /**
     * Czy przekierowywać
     * @param \Mmi\Http\Request $request
     * @return boolean
     */
    protected function _forceNonSslByRequest(\Mmi\Http\Request $request)
    {
        //strona główna
        if ($request->getModuleName() == 'homepage') {
            return true;
        }
        //artykuły
        if ($request->getModuleName() == 'news') {
            return true;
        }
        //strona listy
        if ($request->getModuleName() == 'file' && $request->getControllerName() == 'index' && $request->getActionName() == 'index') {
            return true;
        }
        //strona zasobu
        if ($request->getModuleName() == 'file' && $request->getControllerName() == 'index' && $request->getActionName() == 'display') {
            return true;
        }
        //serwer plików
        if ($request->getModuleName() == 'file' && $request->getControllerName() == 'server') {
            return true;
        }
        //brak wymuszenia
        return false;
    }

}
