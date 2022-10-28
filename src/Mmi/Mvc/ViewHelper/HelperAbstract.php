<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

use Mmi\Mvc\View;

class HelperAbstract
{
    /**
     * Referencja do widoku
     * @var \Mmi\Mvc\View
     */
    public $view;

    /**
     * Metoda programisty końcowego, wykonuje się na końcu konstruktora
     */
    public function init()
    {
    }

    /**
     * Konstruktor, ustawia widok
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        $this->init();
    }
}
