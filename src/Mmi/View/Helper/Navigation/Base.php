<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\View\Helper\Navigation;

/**
 * Helper nawigatora
 */
class Base extends \Mmi\View\Helper\HelperAbstract {

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
	protected $_breadcrumbsData;

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
	 * @var \Mmi\Acl
	 */
	protected static $_acl;

	/**
	 * Obiekt Auth
	 * @var \Mmi\Auth
	 */
	protected static $_auth;

	/**
	 * Ustawia obiekt ACL
	 * @param \Mmi\Acl $acl
	 * @return \Mmi\Acl
	 */
	public static function setAcl(\Mmi\Acl $acl) {
		self::$_acl = $acl;
		return $acl;
	}

	/**
	 * Ustawia obiekt autoryzacji
	 * @param \Mmi\Auth $auth
	 * @return \Mmi\Auth
	 */
	public static function setAuth(\Mmi\Auth $auth) {
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
	 * @return \Mmi\View\Helper\Navigation
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
				$keywords[] = htmlspecialchars($breadcrumb['keywords']);
			}
			//dodawanie opisów
			if (isset($breadcrumb['description'])) {
				$descriptions[] = htmlspecialchars($breadcrumb['description']);
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

}
