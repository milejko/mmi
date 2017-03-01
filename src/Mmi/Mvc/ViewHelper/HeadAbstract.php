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
	 * Pobiera CRC dla danego zasobu lokalnego
	 * @param string $location adres zasobu
	 * @return string
	 */
	protected function _getCrc($location) {
		$cacheKey = 'mmi-head-crc-' . md5($location);
		$cache = $this->view->getCache();
		if (null !== $cache && (null !== ($crc = $cache->load($cacheKey)))) {
			return $crc;
		}
		//obliczanie CRC
		$crc = file_exists($path = BASE_PATH . '/web' . $location) ? crc32(file_get_contents($path)) : 0;
		if (null !== $cache) {
			$cache->save($crc, $cacheKey, 0);
		}
		return $crc;
	}
	
	/**
	 * Zwraca publiczny src z baseUrl i CDN
	 * @param string $src
	 * @return string
	 */
	protected function _getPublicSrc($src) {
		return $this->view->cdn ? $this->view->cdn . $src : $this->view->baseUrl . $src;
	}

}
