<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Service\Weather;

class Ground extends WeatherAbstract {

	/**
	 * Konstruktor wymaga podania klucza API
	 * @param string $apiKey klucz api
	 */
	public function __construct($apiKey) {
		$this->_url = 'http://api.wunderground.com/api/' . $apiKey;
	}

	/**
	 * Wyszukanie po nazwie miejsca
	 * @param string $placeName nazwa miejsca (np. kraj+miasto)
	 * @return \Mmi\Service\Weather\Data aktualna pogoda
	 */
	public function search($placeName) {
		$current = json_decode(file_get_contents($this->_url . '/conditions/forecast/q/' . urlencode($placeName) . '.json'));
		if (!isset($current->current_observation)) {
			throw new\Exception('No data');
		}
		$current = $current->current_observation;
		$wd = new \Mmi\Service\Weather\Data();
		$wd->temperature = $current->temp_c;
		$wd->humidity = trim($current->relative_humidity, '%');
		$wd->windSpeed = $current->wind_kph;
		$wd->windDirection = $current->wind_dir[0];
		$wd->icon = $current->icon_url;
		$wd->condition = $current->weather;
		return $wd;
	}

}
