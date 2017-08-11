<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Navigation;

/**
 * Klasa nawigacji (struktura menu)
 */
class Navigation
{

    /**
     * Klasa kongiguracji
     * @var \Mmi\Navigation\NavigationConfig
     */
    private $_config;

    /**
     * Breadcrumbs
     * @var array
     */
    private $_breadcrumbs = [];

    /**
     * Konstruktor, buduje drzewo na podstawie struktury zagnieżdżonej
     * @param \Mmi\Navigation\NavigationConfig $config konfiguracja nawigatora
     */
    public function __construct(\Mmi\Navigation\NavigationConfig $config)
    {
        $this->_config = $config;
        $config->build();
    }

    /**
     * Określa elementy aktywne, buduje breadcrumbs
     * @param \Mmi\Http\Request $request
     * @return \Mmi\Translate
     */
    public function setup(\Mmi\Http\Request $request)
    {
        //aktywuje liście drzewa
        $activatedTree = $this->_setupActive($this->_config->build, $request->toArray());
        //uzupełnia breadcrumbs na podstawie aktywnych
        if (isset($activatedTree['tree'][0]['children'])) {
            //ustawia breadcrumby
            $this->_setupBreadcrumbs($activatedTree['tree'][0]['children']);
        }
        return $this;
    }

    /**
     * Wyszukuje element, wraz jego dziećmi, oraz rodzicami
     * @param string $id wyszukiwane id
     * @return array
     */
    public function seek($id)
    {
        //wyszukanie
        return $this->_config->findById($id);
    }

    /**
     * Pobiera breadcrumbs
     * @return array
     */
    public function getBreadcrumbs()
    {
        return $this->_breadcrumbs;
    }

    /**
     * Wykorzystywane przez setup do ustawiania elementów aktywnych
     * @param array $tree poddrzewo
     * @param array $params parametry decydujące o aktywności
     * @return array
     */
    private function _setupActive(&$tree, $params)
    {
        //domyślnie zakładamy, że gałąź jest nieaktywna
        $branchActive = false;
        //iteracja po drzewie
        foreach ($tree as $key => $item) {
            //aktywność tylko jeśli istnieje request
            if (true === $active = isset($item['request'])) {
                //iteracja po requescie w danym itemie
                foreach ($item['request'] as $name => $param) {
                    //jeśli brak zmiennej w parametrach, lub różne
                    if (!isset($params[$name]) || $params[$name] != $param) {
                        //nieaktywny
                        $active = false;
                        break;
                    }
                }
            }
            $tree[$key]['active'] = $active;
            //jeśli aktywny - ustawiamy aktywność gałęzi, jeśli nie - bez zmian
            $branchActive = $active ? true : $branchActive;
            //jeśli element posiada dzieci
            if (isset($item['children'])) {
                //zejście rekurencyjne
                $branch = $this->_setupActive($item['children'], $params);
                $tree[$key]['children'] = $branch['tree'];
                //jeśli gałąź aktywna - aktywacja, jeśli nie - bez zmian
                $tree[$key]['active'] = $branch['active'] ? true : $tree[$key]['active'];
            }
            //optymalizacja wydajności
            if ($tree[$key]['active']) {
                unset($item['children']);
                $branchActive = true;
            }
        }
        //zwrot gałęzi z określeniem czy aktywna
        return ['tree' => $tree, 'active' => $branchActive];
    }

    /**
     * Buduje breadcrumbs z aktywowanego drzewa
     * @param array $tree drzewo
     */
    private function _setupBreadcrumbs($tree)
    {
        //iteracja po drzewie
        foreach ($tree as $item) {
            //jeśli nieaktywny przechodzi do następnego
            if (!$item['active']) {
                continue;
            }
            $this->_breadcrumbs[] = $item;
            //jeśli dzieci
            if (isset($item['children'])) {
                //zejście rekurencyjne
                $this->_setupBreadcrumbs($item['children']);
            }
            //optymalizacja wydajności
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
