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
use \Mmi\Message\MessengerHelper;

/**
 * Klasa kontrolera akcji
 */
class Controller
{

    /**
     * Żądanie
     * @var \Mmi\Http\Request
     */
    protected $_request;

    /**
     * Referencja do odpowiedzi z Front controllera
     * @var \Mmi\Http\Response
     */
    protected $_response;

    /**
     * Widok
     * @var \Mmi\Mvc\View
     */
    public $view;

    /**
     * Konstruktor
     */
    public function __construct(\Mmi\Http\Request $request, \Mmi\Mvc\View $view)
    {
        //request
        $this->_request = $request;
        //ustawienie response
        $this->_response = FrontController::getInstance()->getResponse();
        //ustawienie pola widoku
        $this->view = $view;
        //inicjacja programisty kontrolera
        $this->init();
    }

    /**
     * Magiczne pobranie zmiennej z requestu
     * @param string $name nazwa zmiennej
     * @return mixed
     */
    public final function __get($name)
    {
        //pobiera zmienną z requestu po nazwie zmiennej
        return $this->_request->__get($name);
    }

    /**
     * Magiczne sprawczenie istnienia pola w request
     * @param string $key klucz
     * @return bool
     */
    public function __isset($key)
    {
        //sprawdzenie istenia zmiennej w requescie
        return $this->_request->__isset($key);
    }

    /**
     * Magiczne pobranie zmiennej z requestu
     * @param string $name nazwa zmiennej
     * @param mixed $value wartość
     */
    public final function __set($name, $value)
    {
        //ustawienie zmiennej w requescie
        return $this->_request->__set($name, $value);
    }

    /**
     * Magiczne usunięcie zmiennej z requestu
     * @param string $name nazwa zmiennej
     */
    public final function __unset($name)
    {
        //usunięcie zmiennej z requestu
        return $this->_request->__unset($name);
    }

    /**
     * Funkcja dla użytkownika ładowana na końcu konstruktora
     */
    public function init()
    {
        
    }

    /**
     * Pobiera request
     * @return \Mmi\Http\Request
     */
    public final function getRequest()
    {
        return $this->_request;
    }

    /**
     * Zwraca dane post z requesta
     * @return \Mmi\Http\RequestPost
     */
    public final function getPost()
    {
        return $this->getRequest()->getPost();
    }

    /**
     * Zwraca pliki z requesta
     */
    public final function getFiles()
    {
        return $this->getRequest()->getFiles();
    }

    /**
     * Pobiera response
     * @return \Mmi\Http\Response
     */
    public final function getResponse()
    {
        return $this->_response;
    }

    /**
     * Pobiera helper messengera
     * @return \Mmi\Message\Messenger
     */
    public final function getMessenger()
    {
        return MessengerHelper::getMessenger();
    }

    /**
     * Pobiera helper akcji
     * @return \Mmi\Mvc\ActionHelper
     */
    public final function getActionHelper()
    {
        return ActionHelper::getInstance();
    }

    /**
     * Pobiera helper logowania
     * @return \Psr\Log\LoggerInterface
     */
    public final function getLogger()
    {
        return FrontController::getInstance()->getLogger();
    }

    /**
     * Pobiera profiler
     * @return \Mmi\App\KernelProfiler
     */
    public final function getProfiler()
    {
        return FrontController::getInstance()->getProfiler();
    }

}
