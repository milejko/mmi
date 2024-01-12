<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http;

use Mmi\DataObject;

/**
 * Klasa requesta
 *
 * @property string $module moduł
 * @property string $controller kontroler
 * @property string $action akcja
 */
class Request extends DataObject
{
    /**
     * Get values
     */
    private RequestGet $get;

    /**
     * Post values
     */
    private RequestPost $post;

    /**
     * Cookie values
     */
    private RequestCookie $cookie;

    /**
     * File values
     */
    private RequestFiles $files;

    /**
     * Server values
     */
    private RequestServer $server;

    public function __construct(
        array $query = [],
        array $post = [],
        array $cookie = [],
        array $files = [],
        array $server = []
    ) {
        //populate for use with ::__get()
        parent::__construct($query);
        $this->get    = new RequestGet($query);
        $this->post   = new RequestPost($post);
        $this->cookie = new RequestCookie($cookie);
        $this->files  = new RequestFiles($files);
        $this->server = new RequestServer($server);
    }

    /**
     * Creates request from globals
     */
    public static function createFromGlobals(): self
    {
        //returns the new instance
        return new self(
            $_GET,
            $_POST,
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

    public function getQuery(): RequestGet
    {
        return $this->get;
    }

    public function getPost(): RequestPost
    {
        return $this->post;
    }

    public function getCookie(): RequestFiles
    {
        return $this->cookie;
    }

    public function getFiles(): RequestFiles
    {
        return $this->files;
    }

    public function getServer(): RequestServer
    {
        return $this->server;
    }

    /**
     * Zwraca Content-Type żądania
     * @return string
     */
    public function getContentType()
    {
        return $this->getServer()->contentType;
    }

    /**
     * Zwraca metodę żądania (np. GET, POST, PUT)
     * @return string
     */
    public function getRequestMethod()
    {
        return $this->getServer()->requestMethod;
    }

    /**
     * Pobiera nagłówek żądania
     * @param string $name np. Accept-Encoding
     * @return string
     */
    public function getHeader($name)
    {
        $headerName = strtoupper(preg_replace("/[^a-zA-Z0-9_]/", '_', $name));
        return $this->getServer()->__get('HTTP_' . $headerName);
    }

    /**
     * Zwraca referer, lub stronę główną jeśli brak
     * @return string
     */
    public function getReferer(): string
    {
        return filter_var($this->getServer()->httpReferer);
    }

    /**
     * Zwraca moduł
     * @return string
     */
    public function getModuleName()
    {
        return (string)$this->module;
    }

    /**
     * Zwraca kontroler
     * @return string
     */
    public function getControllerName()
    {
        return (string)$this->controller;
    }

    /**
     * Zwraca akcję
     * @return string
     */
    public function getActionName()
    {
        return (string)$this->action;
    }

    /**
     * Ustawia moduł
     * @param string $value
     * @return Request
     */
    public function setModuleName($value)
    {
        $this->module = $value;
        return $this;
    }

    /**
     * Ustawia kontroler
     * @param string $value
     * @return Request
     */
    public function setControllerName($value)
    {
        $this->controller = $value;
        return $this;
    }

    /**
     * Ustawia akcję
     * @param string $value
     * @return Request
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
