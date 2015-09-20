<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\View\Helper;

/**
 * Helper nawigatora
 */
class Navigation extends Navigation\Base {

	/**
	 * Ustawia obiekt nawigatora
	 * @param \Mmi\Navigation\Component $navigation
	 * @return \Mmi\Navigation\Component
	 */
	public static function setNavigation(\Mmi\Navigation\Component $navigation) {
		self::$_navigation = $navigation;
		return $navigation;
	}

	/**
	 * Ustawia maksymalną głębokość
	 * @param int $depth maksymalna głębokość
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setMaxDepth($depth = 1000) {
		$this->_maxDepth = $depth;
		return $this;
	}

	/**
	 * Ustawia minimalną głębokość
	 * @param int $depth minimalna głębokość
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setMinDepth($depth = 0) {
		$this->_minDepth = $depth;
		return $this;
	}

	/**
	 * Ustawia rendering wyłącznie głównej gałęzi
	 * @param boolean $active aktywna
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setActiveBranchOnly($active = true) {
		$this->_activeBranch = $active;
		return $this;
	}

	/**
	 * Ustawia rendering wyłącznie dozwolonych elementów
	 * @param boolean $allowed dozwolone
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setAllowedOnly($allowed = true) {
		$this->_allowedOnly = $allowed;
		return $this;
	}

	/**
	 * Ustawia węzeł startowy
	 * @param int $key klucz
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setRoot($key) {
		$this->setMinDepth();
		$this->setMaxDepth();
		$this->_root = $key;
		return $this;
	}

	/**
	 * Ustawia tytuł
	 * @param string $title tytuł
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}

	/**
	 * Ustawia opis
	 * @param string $description opis
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setDescription($description) {
		$this->_description = $description;
		return $this;
	}

	/**
	 * Ustawia słowa kluczowe
	 * @param string $keywords słowa kluczowe
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setKeywords($keywords) {
		$this->_keywords = $keywords;
		return $this;
	}

	/**
	 * Ustawia okruchy
	 * @param string $breadcrumbs okruchy
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setBreadcrumbs($breadcrumbs) {
		$this->_breadcrumbs = $breadcrumbs;
		return $this;
	}

	/**
	 * Linkuj ostatni breadcrumb w ścieżce
	 * @param bool $link linkuj
	 */
	public function setLinkLastBreadcrumb($link = true) {
		$this->_linkLastBreadcrumb = $link;
		return $this;
	}

	/**
	 * Zwraca bieżącą głębokość w menu
	 * @return int
	 */
	public function getCurrentDepth() {
		$depth = count($this->_breadcrumbsData) - 1;
		return ($depth > 0) ? $depth : 0;
	}

	/**
	 * Zwraca breadcrumbs
	 * @return string
	 */
	public function breadcrumbs() {
		return $this->_breadcrumbs;
	}

	/**
	 * Zwraca tytuł aktywnej strony
	 * @return string
	 */
	public function title() {
		return $this->_title;
	}

	/**
	 * Zwraca słowa kluczowe aktywnej strony
	 * @return string
	 */
	public function keywords() {
		return str_replace(['&amp;nbsp;', '&amp;oacute;', '-  -'], [' ', 'ó', '-'], $this->_keywords);
	}

	/**
	 * Zwraca opis aktywnej strony
	 * @return string
	 */
	public function description() {
		return trim(str_replace(['&amp;nbsp;', '&amp;oacute;', '-  -'], [' ', 'ó', '-'], strip_tags($this->_description)), ' -');
	}

	/**
	 * Metoda główna, zwraca swoją instancję
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function navigation() {
		//brak wygenerowanych breadcrumbów
		if (null === $this->_breadcrumbs) {
			return $this->_buildBreadcrumbs();
		}
		//ustawienia domyślne
		$this->_maxDepth = 1000;
		$this->_minDepth = 0;
		$this->_activeBranch = false;
		$this->_allowedOnly = true;
		return $this;
	}

	/**
	 * Modyfikuje breadcrumbs
	 * @param int $index indeks
	 * @param string $label etykieta
	 * @param string $uri URL
	 * @param string $title tytuł
	 * @param string $description opis
	 * @param string $keywords słowa kluczowe
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function modifyBreadcrumb($index, $label, $uri = null, $title = null, $description = null, $keywords = null) {
		//brak breadcrumbów
		if (!isset($this->_breadcrumbsData[$index])) {
			return $this;
		}
		//ustawianie label
		$this->_modifyBreadcrumbData($index, 'label', $label);
		//ustawianie uri
		$this->_modifyBreadcrumbData($index, 'uri', $uri);
		//ustawianie title
		$this->_modifyBreadcrumbData($index, 'title', $title);
		//ustawianie opisu
		$this->_modifyBreadcrumbData($index, 'description', $description);
		//ustawianie słów kluczowych
		$this->_modifyBreadcrumbData($index, 'keywords', $keywords);
		//przebudowa breadcrumba
		return $this->_buildBreadcrumbs();
	}

	/**
	 * Ustawia separator breadcrumbs
	 * @param string $separator separator
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setSeparator($separator) {
		$this->_separator = $separator;
		//przebudowa breadcrumbów
		$this->_buildBreadcrumbs();
		return $this;
	}

	/**
	 * Ustawia seperator w meta
	 * @param string $separator separator
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function setMetaSeparator($separator) {
		$this->_metaSeparator = $separator;
		//przebudowa breadcrumbów
		$this->_buildBreadcrumbs();
		return $this;
	}

	/**
	 * Modyfikuje ostatni breadcrumb
	 * @param string $label etykieta
	 * @param string $uri URL
	 * @param string $title tytuł
	 * @param string $description opis
	 * @param string $keywords słowa kluczowe
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function modifyLastBreadcrumb($label, $uri = null, $title = null, $description = null, $keywords = null) {
		return $this->modifyBreadcrumb(count($this->_breadcrumbsData) - 1, $label, $uri, $title, $description, $keywords);
	}

	/**
	 * Dodaje breadcrumb
	 * @param string $label etykieta
	 * @param string $uri URL
	 * @param string $title tytuł
	 * @param string $description opis
	 * @param string $keywords słowa kluczowe
	 * @param bool $unshift wstaw na początku
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function createBreadcrumb($label, $uri = null, $title = null, $description = null, $keywords = null, $unshift = false) {
		$breadcrumb = [
			'label' => $label,
			'uri' => $uri,
			'title' => $title,
			'description' => $description,
			'keywords' => $keywords
		];
		//wstawienie przed
		if ($unshift) {
			array_unshift($this->breadcrumbsData, $breadcrumb);
		} else {
			//wstawienie po
			$this->_breadcrumbsData[] = $breadcrumb;
		}
		//przebudowa breadcrumbów
		return $this->_buildBreadcrumbs();
	}

	/**
	 * Dodaje breadcrumb na koniec
	 * @param string $label etykieta
	 * @param string $uri URL
	 * @param string $title tytuł
	 * @param string $description opis
	 * @param string $keywords słowa kluczowe
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function appendBreadcrumb($label, $uri = null, $title = null, $description = null, $keywords = null) {
		return $this->createBreadcrumb($label, $uri, $title, $description, $keywords, false);
	}

	/**
	 * Dodaje breadcrumb na początek
	 * @param string $label etykieta
	 * @param string $uri URL
	 * @param string $title tytuł
	 * @param string $description opis
	 * @param string $keywords słowa kluczowe
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function prependBreadcrumb($label, $uri = null, $title = null, $description = null, $keywords = null) {
		return $this->createBreadcrumb($label, $uri, $title, $description, $keywords, true);
	}

	/**
	 * Usuwa ostatni breadcrumb
	 * @return \Mmi\View\Helper\Navigation
	 */
	public function removeLastBreadcrumb() {
		$index = count($this->_breadcrumbsData) - 1;
		//brak breadcrumba
		if (!isset($this->_breadcrumbsData[$index])) {
			return $this;
		}
		//usuwanie
		unset($this->_breadcrumbsData[$index]);
		//przebudowa
		return $this->_buildBreadcrumbs();
	}

	/**
	 * Alias renderera menu
	 * @see \Mmi\View\Helper\Navigation::menu()
	 * @return string
	 */
	public function renderMenu() {
		return $this->menu();
	}

	/**
	 * Renderer menu
	 * @return string
	 */
	public function menu() {
		if (null === self::$_navigation) {
			return '';
		}
		if ($this->_root) {
			$tree = self::$_navigation->seek($this->_root);
		} else {
			$tree = null;
		}
		return $this->_getHtml($tree);
	}

}
