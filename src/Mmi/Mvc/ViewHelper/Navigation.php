<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Mvc\ViewHelper;

/**
 * Helper nawigatora
 */
class Navigation extends \Mmi\Mvc\ViewHelper\HelperAbstract {

	/**
	 * Maksymalna głębokość menu
	 * @var int
	 */
	protected $_maxDepth = 1000;

	/**
	 * Minimalna głębokość menu
	 * @var int
	 */
	protected $_minDepth = 0;

	/**
	 * Separator breadcrumbs
	 * @var string
	 */
	protected $_separator = ' &gt; ';

	/**
	 * Separator breadcrumbs
	 * @var string
	 */
	protected $_metaSeparator = ' - ';

	/**
	 * Renderuje tylko aktywną gałąź
	 * @var boolean
	 */
	protected $_activeBranch = false;

	/**
	 * Renderuje tylko dozwolone w ACL
	 * @var boolean
	 */
	protected $_allowedOnly = true;

	/**
	 * Identyfikator węzła startowego
	 * @var root
	 */
	protected $_root;

	/**
	 * Przechowuje tytuł aktywnej strony
	 * @var string
	 */
	protected $_title;

	/**
	 * Przechowuje breadcrumbs
	 * @var string
	 */
	protected $_breadcrumbs;

	/**
	 * Przechowuje breadcrumbs w postaci tabelarycznej
	 * @var array
	 */
	protected $_breadcrumbsData = [];

	/**
	 * Przechowuje czy ostatni breadcrumb to link
	 * @var bool
	 */
	protected $_linkLastBreadcrumb = false;

	/**
	 * Przechowuje słowa kluczowe aktywnej strony
	 * @var string
	 */
	protected $_keywords;

	/**
	 * Przechowuje opis aktywnej strony
	 * @var string
	 */
	protected $_description;

	/**
	 * Obiekt nawigatora
	 * @var \Mmi\Navigation
	 */
	protected static $_navigation;

	/**
	 * Obiekt ACL
	 * @var \Mmi\Security\Acl
	 */
	protected static $_acl;

	/**
	 * Obiekt Auth
	 * @var \Mmi\Security\Auth
	 */
	protected static $_auth;

	/**
	 * Ustawia obiekt nawigatora
	 * @param \Mmi\Navigation\Navigation $navigation
	 * @return \Mmi\Navigation\Navigation
	 */
	public static function setNavigation(\Mmi\Navigation\Navigation $navigation) {
		self::$_navigation = $navigation;
		return $navigation;
	}

	/**
	 * Ustawia obiekt ACL
	 * @param \Mmi\Security\Acl $acl
	 * @return \Mmi\Security\Acl
	 */
	public static function setAcl(\Mmi\Security\Acl $acl) {
		self::$_acl = $acl;
		return $acl;
	}

	/**
	 * Ustawia obiekt autoryzacji
	 * @param \Mmi\Security\Auth $auth
	 * @return \Mmi\Security\Auth
	 */
	public static function setAuth(\Mmi\Security\Auth $auth) {
		self::$_auth = $auth;
		return $auth;
	}

	/**
	 * Ustawia pole w danych breadcrumba
	 * @param string $index
	 * @param string $field
	 * @param string $value
	 */
	protected function _modifyBreadcrumbData($index, $field, $value) {
		//brak wartości
		if (null === $value) {
			return;
		}
		//ustawienie wartości
		$this->_breadcrumbsData[$index][$field] = $value;
	}

	/**
	 * Sprawdzenie dostępności liścia w ACL
	 * @param array $leaf liść
	 * @return boolean
	 */
	protected function _checkAcl(array $leaf) {
		//sprawdzanie na acl jeśli auth i acl włączone
		if ($this->_allowedOnly && self::$_auth && self::$_acl && $leaf['type'] != 'link' && $leaf['type'] != 'folder') {
			return self::$_acl->isAllowed(self::$_auth->getRoles(), strtolower($leaf['module'] . ':' . $leaf['controller'] . ':' . $leaf['action']));
		}
		return true;
	}

	/**
	 * Buduje breadcrumbs
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	protected function _buildBreadcrumbs() {
		//obiekt nawigatora niezdefiniowany
		if (null === self::$_navigation) {
			return $this;
		}
		//ustawianie danych breadcrumbów
		if (null === ($data = $this->_breadcrumbsData)) {
			$data = self::$_navigation->getBreadcrumbs();
			$this->_breadcrumbsData = $data;
		}
		//błędny format danych
		if (!is_array($data)) {
			return $this;
		}
		$title = [];
		$breadcrumbs = [];
		$keywords = [];
		$descriptions = [];
		$count = count($data);
		$i = 0;
		foreach ($data as $breadcrumb) {
			$i++;
			//dodawanie breadcrumbów
			if (($i == $count && !$this->_linkLastBreadcrumb) || $breadcrumb['uri'] == '#') {
				$breadcrumbs[] = '<span>' . strip_tags($breadcrumb['label']) . '</span>';
			} else {
				$breadcrumbs[] = '<a href="' . $breadcrumb['uri'] . '">' . strip_tags($breadcrumb['label']) . '</a>';
			}
			//dodawanie tytułu
			$title[] = (isset($breadcrumb['title']) && $breadcrumb['title']) ? strip_tags($breadcrumb['title']) : strip_tags($breadcrumb['label']);
			//dodawanie keywords
			if (isset($breadcrumb['keywords'])) {
				$keywords[] = strip_tags($breadcrumb['keywords']);
			}
			//dodawanie opisów
			if (isset($breadcrumb['description'])) {
				$descriptions[] = strip_tags($breadcrumb['description']);
			}
		}
		//ustawianie pól
		return $this->setTitle(trim(implode($this->_metaSeparator, array_reverse($title))))
				->setDescription(trim(implode($this->_metaSeparator, array_reverse($descriptions))))
				->setKeywords(trim(implode(' ', array_reverse($keywords))))
				->setBreadcrumbs(trim(implode($this->_separator, $breadcrumbs)));
	}

	/**
	 * Renderuje drzewo
	 * @param array $tree drzewo
	 * @param int $depth głębokość
	 * @return string
	 */
	protected function _getHtml($tree, $depth = 0) {
		//brak drzewa
		if (empty($tree) || !isset($tree['children'])) {
			return '';
		}
		$menu = $tree['children'];
		//przygotowanie menu do wyświetlenia: usunięcie niedozwolonych i nieaktywnych elementów
		foreach ($menu as $key => $leaf) {
			$leaf['module'] = $leaf['module'] ? : 'default';
			//usuwanie modułu
			if ($leaf['disabled'] || !$leaf['visible'] || !$this->_checkAcl($leaf)) {
				unset($menu[$key]);
			}
		}
		$html = '';
		$index = 0;
		$count = count($menu);
		$childHtml = '';

		//pętla po menu
		foreach ($menu as $leaf) {
			$subHtml = '';
			$recurse = true;
			if ($this->_activeBranch && isset($leaf['active'])) {
				$recurse = $leaf['active'];
			}
			//jeśli liść ma dzieci i nie osiągnięto maksymalnej głębokości
			if (isset($leaf['children']) && $depth < $this->_maxDepth && $recurse) {
				//schodzenie rekurencyjne o 1 poziom w dół
				$subHtml = $this->_getHtml($leaf, $depth + 1);
				$childHtml .= $subHtml;
			}
			//nadawanie klas html
			$class = (isset($leaf['active']) && $leaf['active']) ? 'active current ' : '';
			$class .= ($index == 0) ? 'first ' : '';
			$class .= ($index == ($count - 1)) ? 'last ' : '';
			//jeśli nadano klasę ustawianie
			if ($class) {
				$class = ' class="' . rtrim($class) . '"';
			}
			$extras = '';
			//opcja nofollow
			if (isset($leaf['nofollow']) && $leaf['nofollow'] == 1) {
				$extras .= ' rel="nofollow"';
			}
			//opcja blank
			if (isset($leaf['blank']) && $leaf['blank'] == 1) {
				$extras .= ' target="_blank"';
			}
			//generowanie li
			$html .= '<li id="item-' . $leaf['id'] . '" ' . $class . '><span class="item-begin"></span><a href="' . htmlspecialchars($leaf['uri']) . '"' . $extras . '>' . $leaf['label'] . '</a>' . $subHtml . '<span class="item-end"></span></li>';
			$index++;
		}
		//jeśli renderowanie od minimalnej głębokości
		if ($this->_minDepth > $depth) {
			return $childHtml;
		}
		//jeśli wyrenderowano HTML
		if ($html) {
			return '<ul class="menu depth-' . $depth . '" id="menu-' . $tree['id'] . '">' . $html . '</ul>';
		}
		return '';
	}

	/**
	 * Ustawia maksymalną głębokość
	 * @param int $depth maksymalna głębokość
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function setMaxDepth($depth = 1000) {
		$this->_maxDepth = $depth;
		return $this;
	}

	/**
	 * Ustawia minimalną głębokość
	 * @param int $depth minimalna głębokość
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function setMinDepth($depth = 0) {
		$this->_minDepth = $depth;
		return $this;
	}

	/**
	 * Ustawia rendering wyłącznie głównej gałęzi
	 * @param boolean $active aktywna
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function setActiveBranchOnly($active = true) {
		$this->_activeBranch = $active;
		return $this;
	}

	/**
	 * Ustawia rendering wyłącznie dozwolonych elementów
	 * @param boolean $allowed dozwolone
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function setAllowedOnly($allowed = true) {
		$this->_allowedOnly = $allowed;
		return $this;
	}

	/**
	 * Ustawia węzeł startowy
	 * @param int $key klucz
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function setTitle($title) {
		$this->_title = $title;
		return $this;
	}

	/**
	 * Ustawia opis
	 * @param string $description opis
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function setDescription($description) {
		$this->_description = $description;
		return $this;
	}

	/**
	 * Ustawia słowa kluczowe
	 * @param string $keywords słowa kluczowe
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function setKeywords($keywords) {
		$this->_keywords = $keywords;
		return $this;
	}

	/**
	 * Ustawia okruchy
	 * @param string $breadcrumbs okruchy
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
		return strip_tags($this->_title);
	}

	/**
	 * Zwraca słowa kluczowe aktywnej strony
	 * @return string
	 */
	public function keywords() {
		return str_replace(['&amp;nbsp;', '&amp;oacute;', '-  -'], [' ', 'ó', '-'], strip_tags($this->_keywords));
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
			array_unshift($this->_breadcrumbsData, $breadcrumb);
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @return \Mmi\Mvc\ViewHelper\Navigation
	 */
	public function prependBreadcrumb($label, $uri = null, $title = null, $description = null, $keywords = null) {
		return $this->createBreadcrumb($label, $uri, $title, $description, $keywords, true);
	}

	/**
	 * Usuwa ostatni breadcrumb
	 * @return \Mmi\Mvc\ViewHelper\Navigation
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
	 * @see \Mmi\Mvc\ViewHelper\Navigation::menu()
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
