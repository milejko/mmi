<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

/**
 * Klasa obróbki obrazów
 */
class Image {

	/**
	 * Konwertuje string, lub binaria do zasobu GD
	 * @param mixed $input
	 * @return resource
	 */
	public static function inputToResource($input) {
		//jeśli resource zwrot
		if (gettype($input) == 'resource') {
			return $input;
		}
		try {
			//jeśli krótki content zakłada że to ścieżka pliku
			return imagecreatefromstring((strlen($input) < 1024) ? file_get_contents($input) : $input);
		} catch (\Exception $e) {
			//logujemy potencjalne błędy
			\Mmi\App\ExceptionLogger::log($e);
			return;
		}
	}

	/**
	 * Skaluje i przycina obrazek tak aby pasował do podanych wymiarów, zachowuje proporcje
	 * @param mixed $input wejście
	 * @param int $x wysokość do której chcemy przeskalować obrazek
	 * @param int $y szerokość do której chcemy przeskalować obrazek
	 * @return resource obrazek
	 */
	public static function scaleCrop($input, $x, $y) {
		//brak zasobu
		if (!($input = self::inputToResource($input))) {
			return;
		}
		$width = imagesx($input);
		$height = imagesy($input);

		$scale = max($y / $height, $x / $width);
		$sx = round($width * $scale);
		$sy = round($height * $scale);

		$tmp = imagecreatetruecolor($sx, $sy);
		imagecopyresampled($tmp, $input, 0, 0, 0, 0, $sx, $sy, $width, $height);
		$input = $tmp;

		$tmp = imagecreatetruecolor($x, $y);
		imagecopyresized($tmp, $input, 0, 0, abs($sx - $x) / 2, abs($sy - $y) / 2, $x, $y, $x, $y);
		return $tmp;
	}

	/**
	 * Skaluje obrazek o podany procent zachowując proporcje
	 * @param mixed $input wejście
	 * @param int $percent procent o jaki ma być zmieniony rozmiar obrazka
	 * @return resource obrazek
	 */
	public static function scaleProportional($input, $percent) {
		//brak zasobu
		if (!($input = self::inputToResource($input))) {
			return;
		}
		$width = imagesx($input);
		$height = imagesy($input);
		$sx = round($width * $percent / 100);
		$sy = round($height * $percent / 100);
		$tmp = imagecreatetruecolor($sx, $sy);
		imagecopyresampled($tmp, $input, 0, 0, 0, 0, $sx, $sy, $width, $height);
		return $tmp;
	}

	/**
	 * Skaluje obrazek proporcjonalnie do podanych maksymalnych wymiarów
	 * @param mixed $input wejście
	 * @param int $maxDimX wysokość do której chcemy przeskalować obrazek
	 * @param int $maxDimY szerokość do której chcemy przeskalować obrazek
	 * @return resource obrazek
	 */
	public static function scale($input, $maxDimX, $maxDimY = NULL) {
		//brak zasobu
		if (!($input = self::inputToResource($input))) {
			return;
		}
		$width = imagesx($input);
		$height = imagesy($input);

		if (is_null($maxDimY)) {
			if ($width - $maxDimX > $height - $maxDimX) {
				return self::scalex($input, $maxDimX);
			}
			return self::scaley($input, $maxDimX);
		}
		//obliczanie współczynników
		$ratioX = $maxDimX / $width;
		$ratioY = $maxDimY / $height;

		if ($ratioX < $ratioY && $ratioX < 1) {
			return self::scaleProportional($input, $ratioX * 100);
		}
		if ($ratioY <= $ratioX && $ratioY < 1) {
			return self::scaleProportional($input, $ratioY * 100);
		}
		return $input;
	}

	/**
	 * Pomniejsza obrazek do danej szerokości zachowując proporcje, nie powiększa obrazka.
	 * @param mixed $input wejście
	 * @param int $maxDim szerokość do której chcemy pomniejszyć obrazek
	 * @return resource obrazek
	 */
	public static function scalex($input, $maxDim) {
		return self::scaleMax($input, $maxDim);
	}

	/**
	 * Pomniejsza obrazek do danej wysokości zachowując proporcje, nie powiększa obrazka.
	 * @param mixed $input wejście
	 * @param int $maxDim wysokość do której chcemy pomniejszyć obrazek
	 * @return resource obrazek
	 */
	public static function scaley($input, $maxDim) {
		return self::scaleMax($input, $maxDim, false);
	}

	/**
	 * Pomniejsza obrazek do maksymalnej długości lub szerokości (proporcjonalnie)
	 * @param mixed $input wejście
	 * @param int $maxDim wysokość do której chcemy pomniejszyć obrazek
	 * @param boolean $horizontal
	 * @return resource obrazek
	 */
	public static function scaleMax($input, $maxDim, $horizontal = true) {
		//brak zasobu
		if (!($input = self::inputToResource($input))) {
			return;
		}
		$width = imagesx($input);
		$height = imagesy($input);
		//skalowanie do maksymalnego y
		if (($horizontal ? $width : $height) > $maxDim) {
			$scale = $maxDim / ($horizontal ? $width : $height);
			$sx = round($width * $scale);
			$sy = round($height * $scale);
			$tmp = imagecreatetruecolor($sx, $sy);
			imagecopyresampled($tmp, $input, 0, 0, 0, 0, $sx, $sy, $width, $height);
			$input = $tmp;
		}
		return $input;
	}

	/**
	 * Obraca obrazek o dany kąt wyrażony w stopniach
	 * @param mixed $input wejście
	 * @param int $pivot kąt obrotu
	 * @return resource obrazek
	 */
	public static function rotate($input, $pivot) {
		//brak zasobu
		if (!($input = self::inputToResource($input))) {
			return;
		}
		$x = imagesx($input);
		$y = imagesy($input);
		switch (round(intval($pivot)) % 4) {
			case '0': return $input;
				break;
			case '1':
				$output = imagecreatetruecolor($y, $x);
				for ($i = 0; $i < $x; $i++)
					for ($j = 0; $j < $y; $j++)
						imagesetpixel($output, $j, $x - $i - 1, imagecolorat($input, $i, $j));
				break;
			case '2':
				$output = imagecreatetruecolor($x, $y);
				imagecopyresampled($output, $input, 0, 0, $x - 1, $y - 1, $x, $y, 0 - $x, 0 - $y);
				break;
			case '3':
				$output = imagecreatetruecolor($y, $x);
				for ($i = 0; $i < $x; $i++)
					for ($j = 0; $j < $y; $j++)
						imagesetpixel($output, $y - $j - 1, $i, imagecolorat($input, $i, $j));
				break;
		}
		return $output;
	}

	/**
	 * Wycina fragment obrazka z punktu x,y o danej długości i wysokości
	 * @param mixed $input wejście
	 * @param int $x współrzędna x
	 * @param int $y współrzędna y
	 * @param int $newWidth nowa Szerokość
	 * @param int $newHeight nowa Wysokość
	 * @return resource obrazek
	 */
	public static function crop($input, $x, $y, $newWidth, $newHeight) {
		//brak zasobu
		if (!($input = self::inputToResource($input))) {
			return;
		}
		//obliczanie nowych wartości x i y
		if (imagesx($input) < $newWidth + $x) {
			$newWidth = imagesx($input) - $x;
		}
		if (imagesy($input) < $newHeight + $y) {
			$newHeight = imagesy($input) - $y;
		}
		//wycinanie obrazka
		$destination = imagecreatetruecolor($newWidth, $newHeight);
		imagecopy($destination, $input, 0, 0, $x, $y, $newWidth, $newHeight);
		return $destination;
	}

}
