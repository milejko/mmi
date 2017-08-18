<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

use Mmi\Message\MessengerHelper;

/**
 * Messenger
 */
class Messenger extends HelperAbstract
{
    //szablon
    CONST TEMPLATE = 'mmi/mvc/view-helper/messenger';

    /**
     * Metoda główna, wyświetla i czyści dostępne wiadomości
     * @return string
     */
    public function messenger()
    {
        $messenger = MessengerHelper::getMessenger();
        if (!$messenger->hasMessages()) {
            return;
        }
        $this->view->_messenger = $messenger;
        return $this->view->renderTemplate(self::TEMPLATE);
    }

}
