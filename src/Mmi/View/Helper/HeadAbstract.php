<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\View\Helper;

class HeadAbstract extends HelperAbstract {

	/**
	 * Pobiera CRC dla danego zasobu (lokalnego lub zdalnego)
	 * @param string $location adres zasobu
	 * @return string
	 */
	protected function _getCrc($location) {
		$cacheKey = 'Head-Crc-' . md5($location);
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
			$location = PUBLIC_PATH . substr($location, $baseUrlLength);
			$online = false;
		}
		if (!$online && !file_exists($location)) {
			$crc = 0;
		} else {
			$crc = crc32(file_get_contents($location));
		}
		if ($cache !== null) {
			$cache->save($crc, $cacheKey);
		}
		return $crc;
	}

}
