<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

/**
 * Klasa requesta
 * 
 * @property string $module moduł
 * @property string $controller kontroler
 * @property string $action akcja
 */
class Request extends \Mmi\DataObject
{

    /**
     * Zwraca Content-Type żądania
     * @return string
     */
    public function getContentType()
    {
        return \Mmi\App\FrontController::getInstance()->getEnvironment()->contentType;
    }

    /**
     * Zwraca metodę żądania (np. GET, POST, PUT)
     * @return string
     */
    public function getRequestMethod()
    {
        return \Mmi\App\FrontController::getInstance()->getEnvironment()->requestMethod;
    }

    /**
     * Pobiera nagłówek żądania
     * @param string $name np. Accept-Encoding
     * @return string
     */
    public function getHeader($name)
    {
        $headerName = strtoupper(preg_replace("/[^a-zA-Z0-9_]/", '_', $name));
        return filter_input(INPUT_SERVER, 'HTTP_' . $headerName, FILTER_SANITIZE_SPECIAL_CHARS);
    }

    /**
     * Zwraca zmienne POST w postaci tabeli
     * @return RequestPost
     */
    public function getPost()
    {
        return new RequestPost($_POST);
    }

    /**
     * Zwraca zmienne GET w postaci tabeli
     * @return RequestGet
     */
    public function getGet()
    {
        return new RequestGet($_GET);
    }

    /**
     * Pobiera informacje o zuploadowanych plikach FILES
     * @return RequestFiles
     */
    public function getFiles()
    {
        return new RequestFiles($_FILES);
    }

    /**
     * Zwraca referer, lub stronę główną jeśli brak
     * @return string
     */
    public function getReferer()
    {
        return \Mmi\App\FrontController::getInstance()->getEnvironment()->httpReferer;
    }

    /**
     * Zwraca moduł
     * @return string
     */
    public function getModuleName()
    {
        return $this->module;
    }

    /**
     * Zwraca kontroler
     * @return string
     */
    public function getControllerName()
    {
        return $this->controller;
    }

    /**
     * Zwraca akcję
     * @return string
     */
    public function getActionName()
    {
        return $this->action;
    }

    /**
     * Ustawia moduł
     * @param string $value
     * @return \Mmi\Http\Request
     */
    public function setModuleName($value)
    {
        $this->module = $value;
        return $this;
    }

    /**
     * Ustawia kontroler
     * @param string $value
     * @return \Mmi\Http\Request
     */
    public function setControllerName($value)
    {
        $this->controller = $value;
        return $this;
    }

    /**
     * Ustawia akcję
     * @param string $value
     * @return \Mmi\Http\Request
     */
    public function setActionName($value)
    {
        $this->action = $value;
        return $this;
    }

    /**
     * Pobiera request jako moduł:kontroler:akcja
     * @return string
     */
    public function getAsColonSeparatedString()
    {
        return $this->module . ':' . $this->controller . ':' . $this->action;
    }

}
