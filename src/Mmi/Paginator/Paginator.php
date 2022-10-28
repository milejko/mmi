<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Paginator;

use Mmi\App\App;
use Mmi\Http\Request;
use Mmi\Mvc\View;

/**
 * Klasa paginatora
 *
 * @method \Mmi\Http\Request getRequest() Zwraca obiekt requesta używany przez Paginator
 * @method \Mmi\Paginator\Paginator setRowsCount(integer $count) Ustawia ilość danych do stronnicowania
 * @method \Mmi\Paginator\Paginator setRowsPerPage(integer $count) Ustawia ilość wierszy na stronę
 * @method \Mmi\Paginator\Paginator setPageVariable(string $name) Ustawia nazwę zmiennej sterującej paginatorem
 * @method \Mmi\Paginator\Paginator setShowPages(integer $pages) Ustawia ilość pokazywanych zakładek skoku (stron)
 * @method \Mmi\Paginator\Paginator setPreviousLabel(string $label) Ustawia tekst pod linkiem poprzedniej strony
 * @method \Mmi\Paginator\Paginator setNextLabel(integer $pages) Ustawia tekst pod linkiem następnej strony
 * @method \Mmi\Paginator\Paginator setPage(integer $page) Ustawia aktualną stronę
 *
 * @method integer getRowsCount() Zwraca aktualną ilość wierszy w paginatorze
 * @method integer getRowsPerPage() Zwraca ilość wierszy na stronę
 */
class Paginator extends \Mmi\OptionObject
{
    //ścieżka szablonu
    public const TEMPLATE = 'mmi/paginator/paginator';

    /**
     * Konstruktor, przyjmuje opcje, ustawia wartości domyślne
     * @param array $options opcje
     */
    public function __construct()
    {
        $this->setRowsPerPage(10)
            ->setShowPages(10)
            ->setPreviousLabel('&#171;')
            ->setNextLabel('&#187;')
            ->setHashHref('')
            ->setPageVariable('p')
            ->setRequest(App::$di->get(Request::class)); //domyślny request
    }

    /**
     * Zwraca limit dla bazy danych
     * @return integer
     */
    public function getLimit()
    {
        return $this->getOption('rowsPerPage');
    }

    /**
     * Pobiera numer aktualnej strony
     * @return integer
     */
    public function getPage()
    {
        if ($this->getOption('page')) {
            return $this->getOption('page');
        }
        $requestPage = $this->getRequest()->__get($this->getOption('pageVariable'));
        $page = ($requestPage > 0) ? $requestPage : 1;
        $this->setPage($page);
        return $page;
    }

    /**
     * Zwraca offset (wiersz startowy) dla bazy danych
     * @return integer
     */
    public function getOffset()
    {
        return $this->getOption('rowsPerPage') * ($this->getPage() - 1);
    }

    /**
     * Ustawia obiekt requesta używany przez Paginator
     * @param \Mmi\Http\Request $request
     * @return \Mmi\Paginator\Paginator
     */
    public function setRequest(\Mmi\Http\Request $request)
    {
        return $this->setOption('request', $request);
    }

    /**
     * Ustawia dla każdego linku label
     * @param string $label
     * @return \Mmi\Paginator\Paginator
     */
    public function setHashHref($label)
    {
        // Ignorujemy hash href przy pustym stringu
        if (mb_strlen($label) === 0) {
            return $this->setOption('hashHref', '');
        }
        return $this->setOption('hashHref', '#' . $label);
    }

    /**
     * Zwraca aktualną ilość stron w paginatorze
     * @return integer
     */
    public function getPagesCount()
    {
        if ($this->getRowsPerPage() == 0) {
            return 0;
        }
        return ceil($this->getRowsCount() / $this->getRowsPerPage());
    }

    /**
     * Magiczny rendering paginatora
     * @return string
     */
    public function __toString()
    {
        //jeśli brak rekordów lub nieustawiona ilość na stronę - brak paginatora
        if (!$this->getRowsCount() || !$this->getRowsPerPage()) {
            return '';
        }
        //jeśli mniej niż 2 strony - brak paginatora
        if (2 > $this->getPagesCount()) {
            return '';
        }
        //paginator do widoku
        App::$di->get(View::class)->_paginator = $this;
        //render szablonu
        return App::$di->get(View::class)->renderTemplate(static::TEMPLATE);
    }
}
