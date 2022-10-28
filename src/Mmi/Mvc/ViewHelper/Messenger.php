<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

/**
 * Messenger
 */
class Messenger extends HelperAbstract
{
    //szablon
    public const TEMPLATE = 'mmi/mvc/view-helper/messenger';

    /**
     * Metoda główna, wyświetla i czyści dostępne wiadomości
     * @return string
     */
    public function messenger()
    {
        if (!$this->view->getMessenger()->hasMessages()) {
            return;
        }
        return $this->view->renderTemplate(static::TEMPLATE);
    }
}
