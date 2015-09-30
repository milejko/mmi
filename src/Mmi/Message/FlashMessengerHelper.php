<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Message;

/**
 * Klasa wiadomości
 */
class FlashMessengerHelper {
	
	/**
	 * Obiekt messengera
	 * @var \Mmi\Message\FlashMessenger
	 */
	public static $_messenger;
	
	/**
	 * Pobiera messengera
	 * @return \Mmi\Message\FlashMessenger
	 */
	public static function getMessenger() {
		if (null === self::$_messenger) {
			return self::$_messenger = new FlashMessenger('flash-messenger');
		}
		return self::$_messenger;
	}
	
}