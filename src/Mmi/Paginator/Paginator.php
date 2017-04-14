<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Paginator;

class Paginator extends \Mmi\OptionObject {

	/**
	 * Konstruktor, przyjmuje opcje, ustawia wartości domyślne
	 * @param array $options opcje
	 */
	public function __construct() {
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
	public function getRequest() {
		return $this->getOption('request');
	}

	/**
	 * Zwraca limit dla bazy danych
	 * @return integer
	 */
	public function getLimit() {
		return $this->getOption('rowsPerPage');
	}

	/**
	 * Pobiera numer aktualnej strony
	 * @return integer
	 */
	public function getPage() {
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
	public function getOffset() {
		return $this->getOption('rowsPerPage') * ($this->getPage() - 1);
	}
	
	/**
	 * Ustawia obiekt requesta używany przez Paginator
	 * @param \Mmi\Http\Request $request
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setRequest(\Mmi\Http\Request $request) {
		return $this->setOption('request', $request);
	}
	
	/**
	 * Ustawia ilość danych do stronnicowania
	 * @param integer $count
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setRowsCount($count) {
		return $this->setOption('rowsCount', intval($count));
	}

	/**
	 * Ustawia ilość wierszy na stronę
	 * @param integer $count
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setRowsPerPage($count) {
		return $this->setOption('rowsPerPage', intval($count));
	}

	/**
	 * Ustawia nazwę zmiennej sterującej paginatorem
	 * @param string $name
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setPageVariable($name) {
		return $this->setOption('pageVariable', $name);
	}

	/**
	 * Ustawia ilość pokazywanych zakładek skoku (stron)
	 * @param int $pages
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setShowPages($pages) {
		return $this->setOption('showPages', intval($pages));
	}

	/**
	 * Ustawia tekst pod linkiem poprzedniej strony
	 * @param string $label
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setPreviousLabel($label) {
		return $this->setOption('previousLabel', $label);
	}

	/**
	 * Ustawia tekst pod linkiem następnej strony
	 * @param string $label
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setNextLabel($label) {
		return $this->setOption('nextLabel', $label);
	}

	/**
	 * Ustawia dla każdego linku label
	 * @param string $label
	 * @return \Mmi\Paginator\Paginator
	 */
	public function setHashHref($label) {
		return $this->setOption('hashHref', '#' . $label);
	}

	/**
	 * Zwraca aktualną ilość wierszy w paginatorze
	 * @return integer
	 */
	public function getRowsCount() {
		return ($this->getOption('rowsCount') >= 0) ? $this->getOption('rowsCount') : 0;
	}

	/**
	 * Zwraca ilość wierszy na stronę
	 * @return integer
	 */
	public function getRowsPerPage() {
		return ($this->getOption('rowsPerPage') >= 0) ? $this->getOption('rowsPerPage') : 10;
	}

	/**
	 * Zwraca aktualną ilość stron w paginatorze
	 * @return integer
	 */
	public function getPagesCount() {
		if ($this->getRowsPerPage() == 0) {
			return 0;
		}
		return ceil($this->getRowsCount() / $this->getRowsPerPage());
	}

	/**
	 * Magiczny rendering paginatora
	 * @return string
	 */
	public function __toString() {
		//jeśli brak rekordów lub nieustawiona ilość na stronę - brak paginatora
		if (!$this->getRowsCount() || !$this->getRowsPerPage()) {
			return '';
		}
		//jeśli mniej niż 2 strony - brak paginatora
		$pagesCount = $this->getPagesCount();
		if ($pagesCount < 2) {
			return '';
		}
		$view = \Mmi\App\FrontController::getInstance()->getView();
		/* if (!$this->getPage()) {
		  $this->getOffset();
		  } */

		//ustawienie wartości do generowania HTML
		$showPages = (($this->getOption('showPages') > 2) ? $this->getOption('showPages') : 2) - 2;
		$halfPages = floor($showPages / 2);
		$pageVariable = $this->getOption('pageVariable');
		$page = $this->getPage();
		$previousLabel = $this->getOption('previousLabel');
		$nextLabel = $this->getOption('nextLabel');
		$hashHref = $this->getOption('hashHref');
		
		//moduł, kontroler, akcja na podstawie requesta
		$modCtrlAct = [
			'module' => $this->getRequest()->getModuleName(),
			'controller' => $this->getRequest()->getControllerName(),
			'action' => $this->getRequest()->getActionName()
		];

		//generowanie HTML
		$html = '<div class="paginator">';

		//generowanie guzika wstecz
		if ($page > 1) {
			$firstPage = (($page - 1) > 1) ? ($page - 1) : null;
			$previousUrl = $view->url($modCtrlAct + [$pageVariable => $firstPage]) . $hashHref;
			$view->headLink(['rel' => 'prev', 'href' => $previousUrl]);
			$html .= '<span class="previous page"><a data-page="' . $firstPage . '" href="' . $previousUrl . ' ">' . $previousLabel . '</a></span>';
		} else {
			$html .= '<span class="previous page">' . $previousLabel . '</span>';
		}

		//generowanie strony pierwszej
		if (1 == $page) {
			$html .= '<span class="current page">1</span>';
		} else {
			$html .= '<span class="page"><a data-page="" href="' . $view->url($modCtrlAct + [$pageVariable => null]) . $hashHref . '">1</a></span>';
		}

		//obliczanie zakresów
		$rangeBegin = (($page - $halfPages) > 2) ? ($page - $halfPages) : 2;
		$rangeBeginExcess = $halfPages - ($page - 2);
		$rangeBeginExcess = ($rangeBeginExcess > 0) ? $rangeBeginExcess : 0;

		$rangeEnd = (($page + $halfPages) < $pagesCount) ? ($page + $halfPages) : $pagesCount - 1;
		$rangeEndExcess = $halfPages - ($pagesCount - $page - 1);
		$rangeEndExcess = ($rangeEndExcess > 0) ? $rangeEndExcess : 0;

		$rangeEnd = (($rangeEnd + $rangeBeginExcess) < $pagesCount) ? ($rangeEnd + $rangeBeginExcess) : $pagesCount - 1;
		$rangeBegin = (($rangeBegin - $rangeEndExcess) > 2) ? ($rangeBegin - $rangeEndExcess) : 2;

		//pierwsza strona w zakresie
		if ($rangeBegin > 2) {
			$html .= '<span class="dots page"><a data-page="' . floor((1 + $rangeBegin) / 2) . '" href="' . $view->url($modCtrlAct + [$pageVariable => floor((1 + $rangeBegin) / 2)]) . $hashHref . '">...</a></span>';
		}

		//generowanie stron w zakresie
		for ($i = $rangeBegin; $i <= $rangeEnd; $i++) {
			if ($i == $page) {
				$html .= '<span class="current page">' . $i . '</span>';
			} else {
				$html .= '<span class="page"><a data-page="' . $i . '" href="' . $view->url($modCtrlAct + [$pageVariable => $i]) . $hashHref . '">' . $i . '</a></span>';
			}
		}

		//ostatnia strona w zakresie
		if ($rangeEnd < $pagesCount - 1) {
			$html .= '<span class="dots page"><a data-page="' . ceil(($rangeEnd + $pagesCount) / 2) . '" href="' . $view->url($modCtrlAct + [$pageVariable => ceil(($rangeEnd + $pagesCount) / 2)]) . $hashHref . '">...</a></span>';
		}

		//ostatnia strona w ogóle
		if ($pagesCount == $page) {
			$html .= '<span class="last current page">' . $pagesCount . '</span>';
		} else {
			$html .= '<span class="last page"><a data-page="' . $pagesCount . '" href="' . $view->url($modCtrlAct + [$pageVariable => $pagesCount]) . $hashHref . '">' . $pagesCount . '</a></span>';
		}

		//generowanie guzika następny
		if ($page < $pagesCount) {
			$nextUrl = $view->url($modCtrlAct + [$pageVariable => $page + 1]) . $hashHref;
			$view->headLink(['rel' => 'next', 'href' => $nextUrl]);
			$html .= '<span class="next page"><a data-page="' . ($page + 1) . '" href="' . $nextUrl . '">' . $nextLabel . '</a></span>';
		} else {
			$html .= '<span class="next page">' . $nextLabel . '</span>';
		}
		$html .= '</div>';

		//zwrot html
		return $html;
	}

}
