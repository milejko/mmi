<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2020 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\Http\Request;
use Mmi\Http\Response;

/**
 * Action controller
 */
class Controller
{

    /**
     * Widok
     * @var View
     */
    protected $view;

    /**
     * Referencja do odpowiedzi
     * @var Response
     */
    private $response;

    /**
     * Konstruktor
     */
    public function __construct(
        View $view,
        Response $response
    )
    {
        //injections
        $this->view         = $view;
        $this->response     = $response;
        //init method
        $this->init();
    }

    /**
     * Funkcja dla użytkownika ładowana na końcu konstruktora
     */
    public function init()
    {}

    /**
     * Gets request
     */
    public final function getRequest(): Request
    {
        return $this->view->request;
    }

    /**
     * Gets response
     */
    public final function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Gets messenger
     */
    public final function getMessenger(): Messenger
    {
        return $this->view->getMessenger();
    }

    /**
     * Get from request (deprecated)
     */
    public function __get(string $name)
    {
        return $this->getRequest()->__get($name);
    }

}
