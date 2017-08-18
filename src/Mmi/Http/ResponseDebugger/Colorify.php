<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Http\ResponseDebugger;

/**
 * Klasa kolorowania składni do panelu debugowania aplikacji
 */
class Colorify
{

    /**
     * Wyszukiwane frazy
     * @var array
     */
    protected static $_search = [
        'Array',
        'Object',
        '(',
        ')',
        '[',
        ']',
        '{',
        '}',
        '=>',
        ' = ',
        '`',
        'DESCRIBE',
        'SELECT',
        'UPDATE',
        'FROM',
        'LIMIT',
        'GROUP BY',
        'ORDER BY',
        'ASC',
        'DESC',
        'AS',
    ];

    /**
     * Tablica zamian wyszukanych fraz
     * @var array
     */
    protected static $_replace = [
        '<span style="color: #22cc22; font-weight: bold;">Array</span>',
        '<span style="color: #22cc22; font-weight: bold;">Object</span>',
        '<span style="color: #2222cc; font-weight: bold;">(</span>',
        '<span style="color: #2222cc; font-weight: bold;">)</span>',
        '<span style="color: #cc2222; font-weight: bold;">[</span>',
        '<span style="color: #cc2222; font-weight: bold;">]</span>',
        '<span style="color: #006600; font-weight: bold;">{</span>',
        '<span style="color: #006600; font-weight: bold;">}</span>',
        '<span style="color: #000f66; font-weight: bold;">=></span>',
        '<span style="color: #000f66; font-weight: bold;"> = </span>',
        '<span style="color: #cc2222; font-weight: bold;">`</span>',
        '<span style="color: #000; font-weight: bold;">DESCRIBE</span>',
        '<span style="color: #000; font-weight: bold;">SELECT</span>',
        '<span style="color: #000; font-weight: bold;">UPDATE</span>',
        '<span style="color: #000; font-weight: bold;">FROM</span>',
        '<span style="color: #000; font-weight: bold;">LIMIT</span>',
        '<span style="color: #000; font-weight: bold;">GROUP BY</span>',
        '<span style="color: #000; font-weight: bold;">ORDER BY</span>',
        '<span style="color: #000; font-weight: bold;">ASC</span>',
        '<span style="color: #000; font-weight: bold;">DESC</span>',
        '<span style="color: #000; font-weight: bold;">AS</span>',
    ];

    /**
     * Koloruje składnie
     * @param string $text kod
     * @return string html
     */
    public static function colorify($text)
    {
        $htmldecoded = htmlspecialchars($text);
        $boldSqBracket = preg_replace('/\[([a-zA-Z]+)\]/', '[<span style="color: #000; font-weight: bold;">${1}</span>]', $htmldecoded);
        $boldBracket = preg_replace('/\{([a-zA-Z_ ]+)\}/', '{<span style="color: #000; font-weight: bold;">${1}</span>}', $boldSqBracket);
        return str_replace(self::$_search, self::$_replace, $boldBracket);
    }

}
