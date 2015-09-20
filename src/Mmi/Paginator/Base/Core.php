<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Paginator\Base;

class Core extends \Mmi\OptionObject {

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
		$requestPage = \Mmi\App\FrontController::getInstance()->getView()->request->__get($this->getOption('pageVariable'));
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
	 * Ustawia ilość danych do stronnicowania
	 * @param integer $count
	 * @return \Mmi\Paginator
	 */
	public function setRowsCount($count) {
		return $this->setOption('rowsCount', intval($count));
	}

	/**
	 * Ustawia ilość wierszy na stronę
	 * @param integer $count
	 * @return \Mmi\Paginator
	 */
	public function setRowsPerPage($count) {
		return $this->setOption('rowsPerPage', intval($count));
	}

	/**
	 * Ustawia nazwę zmiennej sterującej paginatorem
	 * @param string $name
	 * @return \Mmi\Paginator
	 */
	public function setPageVariable($name) {
		return $this->setOption('pageVariable', $name);
	}

	/**
	 * Ustawia ilość pokazywanych zakładek skoku (stron)
	 * @param int $pages
	 * @return \Mmi\Paginator
	 */
	public function setShowPages($pages) {
		return $this->setOption('showPages', intval($pages));
	}

	/**
	 * Ustawia tekst pod linkiem poprzedniej strony
	 * @param string $label
	 * @return \Mmi\Paginator
	 */
	public function setPreviousLabel($label) {
		return $this->setOption('previousLabel', $label);
	}

	/**
	 * Ustawia tekst pod linkiem następnej strony
	 * @param string $label
	 * @return \Mmi\Paginator
	 */
	public function setNextLabel($label) {
		return $this->setOption('nextLabel', $label);
	}

	/**
	 * Ustawia dla każdego linku label
	 * @param string $label
	 * @return \Mmi\Paginator
	 */
	public function setHashHref($label) {
		return $this->setOption('hashHref', '#'. $label);
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

}
