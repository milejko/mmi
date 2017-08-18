<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

class HeadAbstract extends HelperAbstract
{

    /**
     * Pobiera CRC dla danego zasobu lokalnego
     * @param string $location adres zasobu
     * @return string
     */
    protected function _getLocationTimestamp($location)
    {
        $cacheKey = 'mmi-head-ts-' . md5($location);
        $cache = $this->view->getCache();
        if (null !== $cache && (null !== ($ts = $cache->load($cacheKey)))) {
            return $ts;
        }
        //obliczanie timestampu
        $ts = file_exists($path = BASE_PATH . '/web' . $location) ? filemtime($path) : 0;
        if (null !== $cache) {
            $cache->save($ts, $cacheKey, 0);
        }
        return $ts;
    }

    /**
     * Zwraca publiczny src z baseUrl i CDN
     * @param string $src
     * @return string
     */
    protected function _getPublicSrc($src)
    {
        return $this->view->cdn ? $this->view->cdn . $src : $this->view->baseUrl . $src;
    }

}
