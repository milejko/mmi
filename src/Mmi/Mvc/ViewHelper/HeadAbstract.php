<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class HeadAbstract extends HelperAbstract {

	/**
	 * Pobiera CRC dla danego zasobu (lokalnego lub zdalnego)
	 * @param string $location adres zasobu
	 * @return string
	 */
	protected function _getCrc($location) {
		$cacheKey = 'mmi-head-crc-' . md5($location);
		$cache = $this->view->getCache();
		if ($cache !== null && (null !== ($crc = $cache->load($cacheKey)))) {
			return $crc;
		}
		//internal
		$online = true;
		if (preg_match('/^http[s]?/i', $location) == 0) {
			if (strrpos($location, '?') !== false) {
				$location = substr($location, 0, strrpos($location, '?'));
			}
			$baseUrlLength = strlen($this->view->baseUrl);
			$location = BASE_PATH . '/web/' . substr($location, $baseUrlLength);
			$online = false;
		}
		if (!$online && !file_exists($location)) {
			$crc = 0;
		} else {
			$crc = crc32(file_get_contents($location));
		}
		if ($cache !== null) {
			$cache->save($crc, $cacheKey, 0);
		}
		return $crc;
	}

}
