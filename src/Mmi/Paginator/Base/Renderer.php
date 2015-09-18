<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Paginator\Base;

class Renderer extends Core {

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
		$view = \Mmi\Controller\Front::getInstance()->getView();
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

		//generowanie HTML
		$html = '<div class="paginator">';

		//generowanie guzika wstecz
		if ($page > 1) {
			$firstPage = (($page - 1) > 1) ? ($page - 1) : null;
			$previousUrl = $view->url([$pageVariable => $firstPage]) . $hashHref;
			$view->headLink(['rel' => 'prev', 'href' => $previousUrl]);
			$html .= '<span class="previous page"><a data-page="' . $firstPage . '" href="' . $previousUrl . ' ">' . $previousLabel . '</a></span>';
		} else {
			$html .= '<span class="previous page">' . $previousLabel . '</span>';
		}

		//generowanie strony pierwszej
		if (1 == $page) {
			$html .= '<span class="current page">1</span>';
		} else {
			$html .= '<span class="page"><a data-page="" href="' . $view->url([$pageVariable => null]) . $hashHref . '">1</a></span>';
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
			$html .= '<span class="dots page"><a data-page="' . floor((1 + $rangeBegin) / 2) . '" href="' . $view->url([$pageVariable => floor((1 + $rangeBegin) / 2)]) . $this->_options['hashHref'] . '">...</a></span>';
		}

		//generowanie stron w zakresie
		for ($i = $rangeBegin; $i <= $rangeEnd; $i++) {
			if ($i == $page) {
				$html .= '<span class="current page">' . $i . '</span>';
			} else {
				$html .= '<span class="page"><a data-page="' . $i . '" href="' . $view->url([$pageVariable => $i]) . $hashHref . '">' . $i . '</a></span>';
			}
		}

		//ostatnia strona w zakresie
		if ($rangeEnd < $pagesCount - 1) {
			$html .= '<span class="dots page"><a data-page="' . ceil(($rangeEnd + $pagesCount) / 2) . '" href="' . $view->url([$pageVariable => ceil(($rangeEnd + $pagesCount) / 2)]) . $hashHref . '">...</a></span>';
		}

		//ostatnia strona w ogóle
		if ($pagesCount == $page) {
			$html .= '<span class="last current page">' . $pagesCount . '</span>';
		} else {
			$html .= '<span class="last page"><a data-page="' . $pagesCount . '" href="' . $view->url([$pageVariable => $pagesCount]) . $hashHref . '">' . $pagesCount . '</a></span>';
		}

		//generowanie guzika następny
		if ($page < $pagesCount) {
			$nextUrl = $view->url([$pageVariable => $page + 1]) . $hashHref;
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
