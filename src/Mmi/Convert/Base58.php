<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Convert;

/**
 * Klasa liczb podstawy 58
 */
class Base58
{

    private static $_alphabet = '123456789abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ';

    /**
     * Konwertuje liczbę całkowitą do liczby o podstawie 58
     * @param integer $num
     * @return string
     */
    public static function encode($num)
    {
        $baseCount = strlen(self::$_alphabet);
        $encoded = '';
        while ($num >= $baseCount) {
            $div = $num / $baseCount;
            $mod = ($num - ($baseCount * intval($div)));
            $encoded = self::$_alphabet[$mod] . $encoded;
            $num = intval($div);
        }
        if ($num) {
            $encoded = self::$_alphabet[$num] . $encoded;
        }
        return $encoded;
    }

    /**
     * Konwertuje liczbę o podstawie 58 do całkowitej
     * @param string $base58
     * @return integer
     */
    public static function decode($base58)
    {
        $len = strlen($base58);
        $decoded = 0;
        $multi = 1;
        for ($i = $len - 1; $i >= 0; $i--) {
            $decoded += $multi * strpos(self::$_alphabet, $base58[$i]);
            $multi = $multi * strlen(self::$_alphabet);
        }
        return $decoded;
    }

}
