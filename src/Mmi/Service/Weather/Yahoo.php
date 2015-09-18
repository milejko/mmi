<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Service\Weather;

class Yahoo extends WeatherAbstract {

	/**
	 * Konstruktor, ustawienie url usługi
	 * @param string $appId Identyfikator aplikacji
	 */
	public function __construct($appId = '') {
		$this->_url = 'http://weather.yahooapis.com/forecastrss?u=c&w=';
		$this->_geoCityUrl = 'http://where.yahooapis.com/v1/places.q';
		$this->_appId = $appId;
	}

	/**
	 * Pobiera woeid miejsca
	 * @param string $placeName nazwa miejsca
	 * @return int woeid
	 */
	public function getWoeid($placeName) {
		$placeName = str_replace(
			['\'', ' '], '+', $placeName);
		$xml = new SimpleXMLElement(file_get_contents($this->_geoCityUrl . '(\'' . $placeName . '\')?appid=' . $this->_appId));
		return $xml->place->woeid;
	}

	/**
	 * Wyszukanie po nazwie miasta
	 * @param string $cityName nazwa miasta
	 * @return \Mmi\Service\Weather\Data aktualna pogoda
	 */
	public function search($cityName) {
		return $this->getByWoeid($this->getWoeid($cityName));
	}

	/**
	 * Pobranie pogody po woeid
	 * @param string $woeid woeid
	 * @return \Mmi\Service\Weather\Data aktualna pogoda
	 */
	public function getByWoeid($woeid) {
		$xml = new SimpleXmlElement(file_get_contents($this->_url . intval($woeid)));

		if (strpos(strtolower($xml->channel->description), 'error')) {
			throw new\Exception('No data');
		}

		$wd = new \Mmi\Service\Weather\Data();
		$wd->temperature = (string) $xml->channel->item->children('yweather', TRUE)->condition->attributes()->temp;
		$wd->condition = str_replace(' ', '-', strtolower((string) $xml->channel->item->children('yweather', TRUE)->condition->attributes()->text));
		$wd->windSpeed = round((string) $xml->channel->children('yweather', TRUE)->wind->attributes()->speed);
		$wd->windDirection = round((string) $xml->channel->children('yweather', TRUE)->wind->attributes()->direction);
		if ($wd->windDirection >= 0 && $wd->windDirection < 15) {
			$wd->windDirection = 'N';
		} elseif ($wd->windDirection >= 15 && $wd->windDirection < 75) {
			$wd->windDirection = 'NE';
		} elseif ($wd->windDirection >= 75 && $wd->windDirection < 105) {
			$wd->windDirection = 'E';
		} elseif ($wd->windDirection >= 105 && $wd->windDirection < 165) {
			$wd->windDirection = 'SE';
		} elseif ($wd->windDirection >= 165 && $wd->windDirection < 195) {
			$wd->windDirection = 'S';
		} elseif ($wd->windDirection >= 195 && $wd->windDirection < 255) {
			$wd->windDirection = 'SW';
		} elseif ($wd->windDirection >= 255 && $wd->windDirection < 285) {
			$wd->windDirection = 'W';
		} elseif ($wd->windDirection >= 285 && $wd->windDirection < 345) {
			$wd->windDirection = 'NW';
		} else {
			$wd->windDirection = 'N';
		}
		$wd->windChill = round((string) $xml->channel->children('yweather', TRUE)->wind->attributes()->chill);

		$wd->humidity = (string) $xml->channel->children('yweather', TRUE)->atmosphere->attributes()->humidity;
		$wd->pressure = round((string) $xml->channel->children('yweather', TRUE)->atmosphere->attributes()->pressure);

		$wd->sunrise = $this->_formatTime((string) $xml->channel->children('yweather', TRUE)->astronomy->attributes()->sunrise);
		$wd->sunset = $this->_formatTime((string) $xml->channel->children('yweather', TRUE)->astronomy->attributes()->sunset);
		$description = (string) $xml->channel->item->description;
		preg_match('/<img src="(http:\/\/[a-z0-9\/\.]+.gif)"\/>/i', $description, $matches);
		if (isset($matches[1])) {
			$wd->icon = $matches[1];
		}
		$current = $wd;
		$this->_forecast = [];
		return $current;
	}

	/**
	 * Formatowanie czasu z 12h na 24h
	 * @param string $time nazwa miejsca (np. kraj+miasto)
	 * @return string - czas 24h
	 */
	protected function _formatTime($time) {
		$time = explode(' ', $time);
		if (isset($time[1]) && $time[1] == 'pm') {
			$time = explode(':', $time[0]);
			$time[0] = $time[0] + 12;
			$time[0] = implode(':', $time);
		}
		return $time[0];
	}

}
