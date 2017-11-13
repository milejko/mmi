<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Image;

/**
 * Klasa obróbki obrazów
 */
class Image
{

    /**
     * Konwertuje string, lub binaria do zasobu GD
     * @param mixed $input
     * @return resource
     */
    public static function inputToResource($input)
    {
        //jeśli resource zwrot
        if (gettype($input) == 'resource') {
            return $input;
        }
        //jeśli krótki content zakłada że to ścieżka pliku
        return imagecreatefromstring((strlen($input) < 1024) ? file_get_contents($input) : $input);
    }

    /**
     * Skaluje i przycina obrazek tak aby pasował do podanych wymiarów, zachowuje proporcje
     * @param mixed $input wejście
     * @param int $x wysokość do której chcemy przeskalować obrazek
     * @param int $y szerokość do której chcemy przeskalować obrazek
     * @return resource obrazek
     */
    public static function scaleCrop($input, $x, $y)
    {
        //brak zasobu
        if (!($input = self::inputToResource($input))) {
            return;
        }
        //badanie rozmiaru obrazu
        $width = imagesx($input);
        $height = imagesy($input);
        //obliczanie skali
        $scale = max($y / $height, $x / $width);
        //obliczanie zeskalowanych wymiarów
        $sx = round($width * $scale);
        $sy = round($height * $scale);
        //cropowanie zeskalowanego obrazu
        return self::crop(self::scale($input, $sx, $sy), abs($sx - $x) / 2, abs($sy - $y) / 2, $x, $y);
    }

    /**
     * Skaluje obrazek o podany procent zachowując proporcje
     * @param mixed $input wejście
     * @param int $percent procent o jaki ma być zmieniony rozmiar obrazka
     * @return resource obrazek
     */
    public static function scaleProportional($input, $percent)
    {
        //brak zasobu
        if (!($input = self::inputToResource($input))) {
            return;
        }
        //badanie rozmiarów obrazu
        $width = imagesx($input);
        $height = imagesy($input);
        //obliczanie rozmiarów po skalowaniu
        $sx = round($width * $percent / 100);
        $sy = round($height * $percent / 100);
        //tworzenie tymczasowego zasobu
        $tmp = imagecreatetruecolor($sx, $sy);
        //zapis przeźroczystości
        self::_saveAlpha($tmp);
        //skalowanie
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
    public static function scale($input, $maxDimX, $maxDimY = null)
    {
        //brak zasobu
        if (!($input = self::inputToResource($input))) {
            return;
        }
        //badanie rozmiaru obrazu
        $width = imagesx($input);
        $height = imagesy($input);
        //nie podano wysokości
        if (null === $maxDimY) {
            //obraz jest szerszy niż wyższy
            if ($width > $height) {
                //skalowanie do maksymalnego X-a
                return self::scalex($input, $maxDimX);
            }
            //obraz jest wyższy niż szerszy, skalowanie do maksymalnego Y-ka
            return self::scaley($input, $maxDimX);
        }
        //obliczanie współczynników
        $ratioX = $maxDimX / $width;
        $ratioY = $maxDimY / $height;
        //skalowanie proporcjonalne ze współczynnikiem X
        if ($ratioX < $ratioY && $ratioX < 1) {
            return self::scaleProportional($input, $ratioX * 100);
        }
        //skalowanie proporcjonalne ze współczynnikiem Y
        if ($ratioY <= $ratioX && $ratioY < 1) {
            return self::scaleProportional($input, $ratioY * 100);
        }
        //zwrot obrazu
        return $input;
    }

    /**
     * Pomniejsza obrazek do danej szerokości zachowując proporcje, nie powiększa obrazka.
     * @param mixed $input wejście
     * @param int $maxDim szerokość do której chcemy pomniejszyć obrazek
     * @return resource obrazek
     */
    public static function scalex($input, $maxDim)
    {
        return self::scaleMax($input, $maxDim);
    }

    /**
     * Pomniejsza obrazek do danej wysokości zachowując proporcje, nie powiększa obrazka.
     * @param mixed $input wejście
     * @param int $maxDim wysokość do której chcemy pomniejszyć obrazek
     * @return resource obrazek
     */
    public static function scaley($input, $maxDim)
    {
        return self::scaleMax($input, $maxDim, false);
    }

    /**
     * Pomniejsza obrazek do maksymalnej długości lub szerokości (proporcjonalnie)
     * @param mixed $input wejście
     * @param int $maxDim wysokość do której chcemy pomniejszyć obrazek
     * @param boolean $horizontal
     * @return resource obrazek
     */
    public static function scaleMax($input, $maxDim, $horizontal = true)
    {
        //brak zasobu
        if (!($input = self::inputToResource($input))) {
            return;
        }
        //badanie rozmiarów obrazu
        $width = imagesx($input);
        $height = imagesy($input);
        //obraz jest już prawidłowy
        if (($horizontal ? $width : $height) <= $maxDim) {
            return $input;
        }
        //obliczanie proporcji i skalowanie proporcjonalne
        return self::scaleProportional($input, ($maxDim / ($horizontal ? $width : $height)) * 100);
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
    public static function crop($input, $x, $y, $newWidth, $newHeight)
    {
        //brak zasobu
        if (!($input = self::inputToResource($input))) {
            return;
        }
        //obliczanie nowej szerokości
        if (($sx = imagesx($input)) < $newWidth + $x) {
            $newWidth = $sx - $x;
        }
        //obliczanie nowej wysokości
        if (($sy = imagesy($input)) < $newHeight + $y) {
            $newHeight = $sy - $y;
        }
        //wycinanie obrazka
        $destination = imagecreatetruecolor($newWidth, $newHeight);
        //zapis przeźroczystości
        self::_saveAlpha($destination);
        //przycinanie
        imagecopy($destination, $input, 0, 0, $x, $y, $newWidth, $newHeight);
        //zwrot
        return $destination;
    }

    /**
     * Zachowanie alphy
     * @param resource $imgRes
     */
    protected static function _saveAlpha($imgRes)
    {
        imagealphablending($imgRes, false);
        imagesavealpha($imgRes, true);
    }

}
