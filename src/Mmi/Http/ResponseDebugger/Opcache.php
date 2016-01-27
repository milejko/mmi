<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Http\ResponseDebugger;

/**
 * Klasa odczytu z Opcache na potrzeby debuggera
 */
class Opcache {

	/**
	 * HTML ze statusem buforowania Opcache
	 * @return string
	 */
	public static function getHtml() {
		//opcache
		if (null !== ($opcache = self::_getOpcacheStatus())) {
			return $opcache;
		}
		//brak opcache
		return '<span style="color: #ff0000; font-weight: bold; font-size: 14px;">OPcache precompiler not found. If no other installed it is not optimal.</span>';
	}
	
	protected static function _getOpcacheStatus() {
		//brak Opcache
		if (!function_exists('opcache_get_configuration') || !function_exists('opcache_get_status')) {
			return;
		}
		//opcache nie udostępnia API
		if (ini_get('opcache.restrict_api')) {
			return '<span style="color: #666; font-weight: bold; font-size: 14px;">OPcache API restricted by directive opcache.restrict_api</span>';
		}
		$opCache = opcache_get_status();
		//opcache nie jest włączony
		if (!$opCache['opcache_enabled']) {
			return '<span style="color: #ff0000; font-weight: bold; font-size: 14px;">OPcache installed, but not enabled <br />Execution not optimal.</span>';
		}
		//zwrot danych z opcache
		return '<p style="margin: 0; padding: 0;">Engine: <b>OPcache</b></p>' .
			'<p style="margin: 0; padding: 0;">Uptime / ratio: <b>' . round((microtime(true) - $opCache['opcache_statistics']['start_time']) / 3600 / 24, 2) . '</b> days / <b>' . round($opCache['opcache_statistics']['opcache_hit_rate'], 2) . '%</b></p>' .
			'<p style="margin: 0; padding: 0;">Hits / misses: <b>' . $opCache['opcache_statistics']['hits'] . '</b> / <b>' . $opCache['opcache_statistics']['misses'] . '</b></p>' .
			'<p style="margin: 0; padding: 0;">Present entries / inserts: <b>' . $opCache['opcache_statistics']['num_cached_keys'] . '</b> / <b>' . $opCache['opcache_statistics']['max_cached_keys'] . '</b></p>' .
			'<p style="margin: 0; padding: 0;">Memory used / available: <b>' . round($opCache['memory_usage']['used_memory'] / 1048576, 2) . '</b> / <b>' . round($opCache['memory_usage']['free_memory'] / 1048576, 2) . ' MB</b></p>';
	}

}
