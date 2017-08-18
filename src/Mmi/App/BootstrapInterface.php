<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\App;

interface BootstrapInterface
{

    /**
     * Parametryzowanie bootstrapa
     * @param string $env nazwa środowiska
     */
    public function __construct();

    /**
     * Uruchomienie bootstrapa
     */
    public function run();
}
