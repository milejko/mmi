<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi;

/**
 * Kontroler powitalny
 */
class IndexController extends Mvc\Controller
{

    //domyślna labelka
    CONST DEFAULT_LABEL = 'It works!';

    /**
     * Akcja główna
     */
    public function index()
    {
        
    }

    /**
     * Akcja testowa
     * @return string
     */
    public function test()
    {
        return self::DEFAULT_LABEL;
    }

    /**
     * Akcja błędu
     */
    public function error()
    {
        //pobranie response
        $this->getResponse()
            //404
            ->setCodeNotFound();
    }

}
