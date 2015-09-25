<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Navigation;

/**
 * Klasa nawigacji (struktura menu)
 */
class Navigation {

	/**
	 * Klasa kongiguracji
	 * @var \Mmi\Navigation\Config
	 */
	private $_config;

	/**
	 * Breadcrumbs
	 * @var array
	 */
	private $_breadcrumbs = [];

	/**
	 * Konstruktor, buduje drzewo na podstawie struktury zagnieżdżonej
	 * @param \Mmi\Navigation\Config $config konfiguracja nawigatora
	 */
	public function __construct(\Mmi\Navigation\Config $config) {
		$this->_config = $config;
		$config->build();
	}

	/**
	 * Określa elementy aktywne, buduje breadcrumbs
	 * @param \Mmi\Http\Request $request
	 * @return \Mmi\Translate
	 */
	public function setup(\Mmi\Http\Request $request) {
		//aktywuje liście drzewa
		$activatedTree = $this->_setupActive($this->_config->build, $request->toArray());
		//uzupełnia breadcrumbs na podstawie aktywnych
		if (isset($activatedTree['tree'][0]['children'])) {
			$this->_setupBreadcrumbs($activatedTree['tree'][0]['children']);
		}
		return $this;
	}

	/**
	 * Wyszukuje element, wraz jego dziećmi, oraz rodzicami
	 * @param string $id wyszukiwane id
	 * @return array
	 */
	public function seek($id) {
		return $this->_config->findById($id);
	}

	/**
	 * Pobiera breadcrumbs
	 * @return array
	 */
	public function getBreadcrumbs() {
		return $this->_breadcrumbs;
	}

	/**
	 * Wykorzystywane przez setup do ustawiania elementów aktywnych
	 * @param array $tree poddrzewo
	 * @param array $params parametry decydujące o aktywności
	 * @return array
	 */
	private function _setupActive(&$tree, $params) {
		$branchActive = false;
		foreach ($tree as $key => $item) {
			$active = true;
			if (!isset($item['request'])) {
				$active = false;
			} else {
				foreach ($item['request'] as $name => $param) {
					if (!isset($params[$name]) || $params[$name] != $param) {
						$active = false;
						break;
					}
				}
			}
			$tree[$key]['active'] = $active;
			if ($active) {
				$branchActive = true;
			}
			if (isset($item['children'])) {
				$branch = $this->_setupActive($item['children'], $params);
				$tree[$key]['children'] = $branch['tree'];
				if ($branch['active']) {
					$tree[$key]['active'] = true;
				}
			}
			if ($tree[$key]['active'] && array_key_exists('visible', $tree[$key])) {
				unset($item['children']);
				$branchActive = true;
			}
		}
		return ['tree' => $tree, 'active' => $branchActive];
	}

	/**
	 * Buduje breadcrumbs z aktywowanego drzewa
	 * @param array $tree drzewo
	 */
	private function _setupBreadcrumbs($tree) {
		foreach ($tree as $item) {
			//jeśli nieaktywny przechodzi do następnego
			if (!$item['active']) {
				continue;
			}
			//jeśli ustawiony moduł lub widoczny dodawanie do breadcrumbs
			if ($item['module'] || $item['visible'] == 1) {
				$this->_breadcrumbs[] = $item;
			}
			//jeśli dzieci - schodzenie rekurencyjne
			if (isset($item['children'])) {
				$this->_setupBreadcrumbs($item['children']);
			}
			break;
		}
		//brak breadcrumbs
		if (count($this->_breadcrumbs) == 0) {
			return;
		}
		//ostatni item
		$currentItem = $this->_breadcrumbs[count($this->_breadcrumbs) - 1];
		//jeśli jest niezależny kasujemy wszystko poza nim
		if (isset($currentItem['independent']) && $currentItem['independent']) {
			$this->_breadcrumbs = [$this->_breadcrumbs[count($this->_breadcrumbs) - 1]];
		}
	}

}
