<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Filter;

/**
 * Filtr dla tinymce
 */
class TinyMce extends \Mmi\Filter\FilterAbstract
{
    /**
     * Filtruje zmienne tak by były poprawnie przekazywane przez GET
     * @param mixed $value wartość
     * @throws \Mmi\App\KernelException jeśli filtrowanie $value nie jest możliwe
     * @return mixed
     */
    public function filter($value)
    {
        return strip_tags($value, '<img><em><b><strong><u><p><a><br><ul><ol><hr><table><th><tbody><thead><tr><td><li><span><div><h1><h2><h3><h4><h5><h6><sup><sub><iframe><code>');
    }
}
