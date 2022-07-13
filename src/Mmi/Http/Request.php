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
     * Get values
     * @var array
     */
    private $get;

    /**
     * Post values
     * @var array
     */
    private $post;

    /**
     * Cookie valuew
     */
    private $cookie;

    /**
     * File values
     */
    private $files;

    /**
     * Server values
     */
    private $server;

    public function __construct(
        array $query        = [],
        array $post         = [],
        array $attributes   = [],
        array $cookie       = [],
        array $files        = [],
        array $server       = []
    )
    {
        //populate for use with ::__get()
        parent::__construct($query);
        $this->get    = $query;
        $this->post   = $post;
        $this->cookie = $cookie;
        $this->files  = $files;
        $this->server = $server;
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
            [],
            $_COOKIE,
            $_FILES,
            $_SERVER
        );
    }

    public function getPost(): RequestPost
    {
        return new RequestPost($this->post);
    }

    public function getServer(): RequestServer
    {
        return new RequestServer($this->server);
    }

    public function getQuery(): RequestGet
    {
        return new RequestGet($this->get);
    }

    public function getFiles(): RequestFiles
    {
        return new RequestFiles($this->files);
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
        return filter_var(isset($this->server['HTTP_REFERER']) ? $this->server['HTTP_REFERER'] : '');
    }

    /**
     * Zwraca moduł
     * @return string
     */
    public function getModuleName()
    {
        return (string) $this->module;
    }

    /**
     * Zwraca kontroler
     * @return string
     */
    public function getControllerName()
    {
        return (string) $this->controller;
    }

    /**
     * Zwraca akcję
     * @return string
     */
    public function getActionName()
    {
        return (string) $this->action;
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
