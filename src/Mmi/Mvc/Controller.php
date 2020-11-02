<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\Http\Response;

/**
 * Klasa kontrolera akcji
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
     * @var Messenger
     */
    private $messenger;

    /**
     * Konstruktor
     */
    public function __construct(
        View $view,
        Response $response, 
        Messenger $messenger
    )
    {
        //injections
        $this->view         = $view;
        $this->response     = $response;
        $this->messenger    = $messenger;
        //init method
        $this->init();
    }

    /**
     * Funkcja dla użytkownika ładowana na końcu konstruktora
     */
    public function init()
    {}

    /**
     * Pobiera response
     */
    public final function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Pobiera helper messengera
     */
    public final function getMessenger(): Messenger
    {
        return $this->messenger;
    }

}
