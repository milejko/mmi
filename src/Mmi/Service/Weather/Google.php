<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Service\Weather;

class Google extends WeatherAbstract {

	/**
	 * Ścieżka bazowa do ikon
	 * @var string
	 */
	protected $_iconBaseUrl = 'http://www.google.com';

	/**
	 * Konstruktor, ustawienie url usługi
	 */
	public function __construct() {
		$this->_url = 'http://www.google.com/ig/api?weather';
	}

	/**
	 * Wyszukanie po nazwie miejsca
	 * @param string $placeName nazwa miejsca (np. kraj+miasto)
	 * @return \Mmi\Service\Weather\Data aktualna pogoda
	 */
	public function search($placeName) {
		//wczytanie XML
		$xml = new SimpleXMLElement(preg_replace('/<(city|postal_code) data="(.[^>]+)"\/>/', '<$1 data=""/>', file_get_contents($this->_url . '=' . urlencode($placeName))));
		//nowy element danych
		$wd = new \Mmi\Service\Weather\Data();

		//pobranie bieżących warunków
		$current = $xml->weather->current_conditions;
		if (!isset($xml->weather->current_conditions)) {
			throw new \Mmi\Service\Exception('No data');
		}
		//warunki pogodowe
		$wd->condition = (string) $current->condition->attributes()->data;
		//temperatura
		$wd->temperature = (string) $current->temp_c->attributes()->data;
		//wilgotność
		$wd->humidity = (string) $current->humidity->attributes()->data;
		$wd->humidity = substr($wd->humidity, 10, -1);
		//ikona
		$wd->icon = $this->_iconBaseUrl . (string) $current->icon->attributes()->data;

		//obliczenia danych o wietrze
		if (isset($current->wind_condition)) {
			$wind = (string) $current->wind_condition->attributes()->data;
			$wind = substr($wind, 6);
			$wd->windDirection = trim(substr($wind, 0, 2));
			$windMph = trim(substr($wind, 5, -4));
			$wd->windSpeed = round($windMph * 1.609);
		}

		return $wd;
		/*foreach ($xml->weather->forecast_conditions as $forecast) {
			$wd = new \Mmi\Service\Weather\Data();
			$wd->condition = (string) $forecast->condition->attributes()->data;
			$minFahrenheit = (string) $forecast->low->attributes()->data;
			$maxFahrenheit = (string) $forecast->high->attributes()->data;
			$minCelsius = ($minFahrenheit - 32) * 5 / 9;
			$maxCelsius = ($maxFahrenheit - 32) * 5 / 9;
			$wd->temperature = round(($minCelsius + $maxCelsius) / 2);
			$wd->icon = $this->_iconBaseUrl . (string) $forecast->icon->attributes()->data;
			$this->_forecast[] = $wd;
		}*/
	}

}
