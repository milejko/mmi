<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

class RouterConfigRoute
{

    /**
     * Nazwa routy (unikalna)
     * @var string
     */
    public $name;

    /**
     * Wyrażenie regularne, lub czysty tekst, np.:
     * /^hit\/(.[^\/]+)/
     * witaj/potwierdzenie
     * @var string
     */
    public $pattern;

    /**
     * Tabela zastąpień, np.:
     * array('module' => 'news', 'controller' => 'index', 'action' => 'index');
     * @var array
     */
    public $replace = [];

    /**
     * Tabela wartości domyślnych, np.:
     * array('lang' => 'pl');
     * @var array
     */
    public $default = [];

}
