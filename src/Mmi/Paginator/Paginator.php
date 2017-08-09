<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Paginator;

/**
 * Klasa paginatora
 */
class Paginator extends \Mmi\OptionObject
{
    
    //ścieżka szablonu
    const TEMPLATE = 'mmi/paginator/paginator';

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
            ->setRequest(\Mmi\App\FrontController::getInstance()->getView()->request); //domyślny request
    }

    /**
     * Zwraca obiekt requesta używany przez Paginator
     * @return \Mmi\Http\Request
     */
    public function getRequest()
    {
        return $this->getOption('request');
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
        $this->setOption('page', $page);
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
     * Ustawia ilość danych do stronnicowania
     * @param integer $count
     * @return \Mmi\Paginator\Paginator
     */
    public function setRowsCount($count)
    {
        return $this->setOption('rowsCount', intval($count));
    }

    /**
     * Ustawia ilość wierszy na stronę
     * @param integer $count
     * @return \Mmi\Paginator\Paginator
     */
    public function setRowsPerPage($count)
    {
        return $this->setOption('rowsPerPage', intval($count));
    }

    /**
     * Ustawia nazwę zmiennej sterującej paginatorem
     * @param string $name
     * @return \Mmi\Paginator\Paginator
     */
    public function setPageVariable($name)
    {
        return $this->setOption('pageVariable', $name);
    }

    /**
     * Ustawia ilość pokazywanych zakładek skoku (stron)
     * @param int $pages
     * @return \Mmi\Paginator\Paginator
     */
    public function setShowPages($pages)
    {
        return $this->setOption('showPages', intval($pages));
    }

    /**
     * Ustawia tekst pod linkiem poprzedniej strony
     * @param string $label
     * @return \Mmi\Paginator\Paginator
     */
    public function setPreviousLabel($label)
    {
        return $this->setOption('previousLabel', $label);
    }

    /**
     * Ustawia tekst pod linkiem następnej strony
     * @param string $label
     * @return \Mmi\Paginator\Paginator
     */
    public function setNextLabel($label)
    {
        return $this->setOption('nextLabel', $label);
    }

    /**
     * Ustawia dla każdego linku label
     * @param string $label
     * @return \Mmi\Paginator\Paginator
     */
    public function setHashHref($label)
    {
        return $this->setOption('hashHref', '#' . $label);
    }

    /**
     * Zwraca aktualną ilość wierszy w paginatorze
     * @return integer
     */
    public function getRowsCount()
    {
        return ($this->getOption('rowsCount') >= 0) ? $this->getOption('rowsCount') : 0;
    }

    /**
     * Zwraca ilość wierszy na stronę
     * @return integer
     */
    public function getRowsPerPage()
    {
        return ($this->getOption('rowsPerPage') >= 0) ? $this->getOption('rowsPerPage') : 10;
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
        \Mmi\App\FrontController::getInstance()->getView()->_paginator = $this;
        //render szablonu
        return \Mmi\App\FrontController::getInstance()->getView()->renderTemplate(self::TEMPLATE);
    }

}
