<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Message;

/**
 * Klasa wiadomości
 */
class MessengerHelper
{

    /**
     * Obiekt messengera
     * @var \Mmi\Message\Messenger
     */
    public static $_messenger;

    /**
     * Pobiera messengera
     * @return \Mmi\Message\Messenger
     */
    public static function getMessenger()
    {
        if (null === self::$_messenger) {
            return self::$_messenger = new Messenger('messenger');
        }
        return self::$_messenger;
    }

}
