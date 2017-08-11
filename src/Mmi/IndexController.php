<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Kontroler powitalny
 */
class IndexController extends Mvc\Controller
{

    /**
     * Akcja główna
     */
    public function indexAction()
    {
        
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
