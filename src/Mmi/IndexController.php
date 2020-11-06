<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi;

use Mmi\Mvc\Controller;

/**
 * Kontroler powitalny
 */
class IndexController extends Controller
{

    //domyślna labelka
    CONST DEFAULT_LABEL = '<html><body><h1>It works!</h1></body></html>';

    /**
     * Akcja główna
     */
    public function indexAction()
    {
        return self::DEFAULT_LABEL;
    }

    /**
     * Akcja testowa
     * @return string
     */
    public function testAction()
    {
        return self::DEFAULT_LABEL;
    }

    /**
     * Akcja błędu
     */
    public function errorAction()
    {
        //pobranie response
        $this->getResponse()
            //404
            ->setCodeNotFound();
    }

}
