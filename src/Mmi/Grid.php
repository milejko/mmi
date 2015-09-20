<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi;

abstract class Grid {

	/**
	 * Komunikaty
	 */
	const ON = 'Zaznaczony';
	const OFF = 'Odznaczony';
	const DELETE = 'Czy na pewno usunąć';
	const COUNTER = '#';

	/**
	 * Identyfikator grida
	 * @var string
	 */
	protected $_id;

	/**
	 * Zapytanie filtrujące
	 * @var \Mmi\Orm\Query
	 */
	protected $_daoQuery;

	/**
	 * Przechowuje kolumny
	 * @var array
	 */
	protected $_columns;

	/**
	 * Przechowuje opcje
	 * @var array
	 */
	protected $_options;

	/**
	 * Referencja do widoku
	 * @var \Mmi\View
	 */
	protected $_view;

	/**
	 * Referencja do żądania
	 * @var \Mmi\Controller\Request
	 */
	protected $_request;

	/**
	 * Przechowuje dane
	 * @var array
	 */
	protected $_data;

	/**
	 * Przechowuje ilość rekordów
	 * @var int
	 */
	protected $_dataCount;

	/**
	 * Określa czy grid rozpoczął renderowanie
	 * @var boolean
	 */
	protected $_renderStarted;

	/**
	 * Konstruktor, ustawia wartości domyślne, referencje do obiektów i tworzy model
	 *
	 * @param array $options opcje
	 * @throws exception
	 */
	public function __construct(array $options = []) {
		$this->_view = \Mmi\App\FrontController::getInstance()->getView();
		$this->_request = $this->_view->request;
		$this->_setDefaultOptions();
		$this->_view->headScript()->prependFile($this->_view->baseUrl . '/resource/cms/js/jquery/jquery.js');
		$this->_view->headScript()->appendFile($this->_view->baseUrl . '/resource/cms/js/jquery/ui.js');
		$this->_view->headScript()->appendFile($this->_view->baseUrl . '/resource/cms/js/grid.js');
		$class = get_class($this);
		$this->_id = strtolower(substr($class, strrpos($class, '\\') + 1));
		$this->setOptions($options);
		$this->init();
		$this->_renderStarted = false;
	}

	/**
	 * Renderer
	 * @return string
	 */
	public function __toString() {
		try {
			$data = $this->render();
		} catch (\Exception $e) {
			$data = 'Grid failed: ' . dump($e);
		}
		return $data;
	}

	/**
	 * Zwraca identyfikator grid'a
	 * @return string
	 */
	public function getId() {
		return $this->_id;
	}

	/**
	 * Abstrakcyjna funkcja użytkownika, do nadpisania przy tworzeniu nowego grida
	 */
	public abstract function init();

	/**
	 * Ustawia wszystkie opcje
	 * @param array $options opcje
	 * @return \Mmi\Grid
	 */
	public function setOptions(array $options = []) {
		foreach ($options as $key => $value) {
			$this->setOption($key, $value);
		}
		return $this;
	}

	/**
	 * Ustawia pojedynczą opcję
	 * @param string $name nazwa
	 * @param mixed $value wartość
	 * @return \Mmi\Grid
	 */
	public function setOption($name, $value) {
		$this->_options[$name] = $value;
		$options = new \Mmi\Session\Space(get_class($this));
		$options->options = $this->_options;
		return $this;
	}

	/**
	 * Pobiera opcję po nazwie
	 * @param string $name
	 * @return \Mmi\Grid
	 */
	public function getOption($name) {
		return isset($this->_options[$name]) ? $this->_options[$name] : null;
	}

	/**
	 * Ustawia startowe zapytanie filtrujące
	 * @param \Mmi\Orm\Query $query
	 * @return \Mmi\Grid
	 */
	public function setQuery(\Mmi\Orm\Query $query) {
		$this->_daoQuery = $query;
		return $this;
	}

	/**
	 * Zwraca obiekt zapytania filtrującego
	 * @return \Mmi\Orm\Query
	 */
	public function getQuery() {
		return $this->_daoQuery;
	}

	/**
	 * Dodaje kolumnę
	 * @param string $type typ: text|checkbox|select|custom
	 * @param string $name nazwa
	 * @param array $options opcje
	 * @return \Mmi\Grid
	 */
	public function addColumn($type, $name, array $options = []) {
		switch ($type) {
			case 'text':
				$options['class'] = 'text';
				break;
			case 'image':
				$options['class'] = 'image';
				$options['writeable'] = false;
				$options['sortable'] = false;
				$options['seekable'] = false;
				break;
			case 'checkbox':
				$options['class'] = 'checkbox';
				break;
			case 'select':
				$options['class'] = 'select';
				break;
			case 'custom':
				$options['class'] = 'custom';
				break;
			case 'buttons':
				$options['class'] = 'custom';
				$options['writeable'] = false;
				$options['sortable'] = false;
				$options['seekable'] = false;
				break;
			case 'counter':
				$options['label'] = self::COUNTER;
				$options['class'] = 'counter';
				$options['writeable'] = false;
				$options['sortable'] = false;
				break;
			default:
				throw new exception('Invalid type: "' . $type . '" passed as column type for: "' . $name . '"');
		}
		$options['type'] = $type;
		$options['translate'] = isset($options['translate']) ? $options['translate'] : true;
		$options['writeable'] = isset($options['writeable']) ? $options['writeable'] : true;
		$options['sortable'] = isset($options['sortable']) ? $options['sortable'] : true;
		$options['seekable'] = isset($options['seekable']) ? $options['seekable'] : true;
		$options['append'] = isset($options['append']) ? $options['append'] : true;
		$options['label'] = isset($options['label']) ? $options['label'] : $name;
		$options['name'] = $name;
		$options['links'] = isset($options['links']) ? $options['links'] : false;
		if ($options['append']) {
			$this->_columns[] = $options;
		} else {
			array_unshift($this->_columns, $options);
		}
		return $this;
	}

	/**
	 * Renderuje grid
	 * @return string
	 */
	public function render() {
		//sprawdzanie query
		if (!($this->_daoQuery instanceof \Mmi\Orm\Query)) {
			throw new \Exception('\Mmi\Grid: invalid DAO Query object supplied');
		}
		$html = '<form id="' . $this->_id . '">';
		if ($this->getOption('sortableAction')) {
			$html .= '<div class="sortable" data-src="'. $this->getOption('sortableAction') .'">';
		}
		$html .= '<table class="striped ' . $this->_options['class'] . '">';
		$html .= $this->renderHead();
		$html .= '<tbody id="' . $this->_id . '_body">';
		$html .= $this->renderBody();
		$html .= '</tbody>';
		$html .= '</table>';
		if ($this->getOption('sortableAction')) {
			$html .= '</div>';
		}
		$html .= '</form>';
		return $html;
	}

	/**
	 * Renderuje ciało tabeli
	 * @return string
	 */
	public function renderBody() {
		if (!$this->_renderStarted) {
			$this->_setDefaultData();
			$this->_setDefaultColumns();
			$this->_renderStarted = true;
		}
		$html = '';
		$counter = ($this->_options['page'] - 1) * $this->_options['rows'] + 1;
		foreach ($this->_data as $rowData) {
			$html .= $this->_buildColumns($rowData, $counter++);
		}
		if (count($this->_data) < 1) {
			$html .= '<tr><td class="empty" colspan="' . count($this->_columns) . '">' . $this->_view->getTranslate()->_('Nie odnaleziono wyników') . '</td></tr>';
		}
		if ($this->_options['pagination'] === true) {
			$html .= $this->renderPaging();
		}
		$html .= '<tr style="display: none;"><td><input type="hidden" id="' . $this->_id . '__ctrl" value="' . \Mmi\Convert\Table::toString($this->_options) . '" />';
		$html .= '<input type="hidden" id="' . $this->_id . '__counter" value="' . ceil($this->_dataCount / $this->_options['rows']) . '" /></td></tr>';
		return $html;
	}

	/**
	 * Renderuje stronnicowanie (ilość elementów na stronie)
	 * @return string
	 */
	public function renderPaging() {
		$html = '<tr><th class="footer" colspan="' . count($this->_columns) . '">';

		$html .= $this->_view->getTranslate()->_('Strona') . ': ';
		$html .= '<select id="' . $this->_id . '-filter-counter" class="grid-spot" style="width: 65px;">';
		foreach ($this->_getPagesCount() as $page => $label) {
			$html .= '<option value="' . $page . '"' . (($this->_options['page'] == $page) ? ' selected="selected"' : '') . '>' . $label . '</option>';
		}
		$html .= '</select>';
		$html .= ' ' . $this->_view->getTranslate()->_('ilość wierszy na stronie') . ': ';
		$html .= '<select id="' . $this->_id . '-filter-setRowsPerPage" class="grid-spot" style="width: 65px;">';
		$html .= '<option value="10"' . (($this->_options['rows'] == 10) ? ' selected="selected"' : '') . '>10</option>';
		$html .= '<option value="20"' . (($this->_options['rows'] == 20) ? ' selected="selected"' : '') . '>20</option>';
		$html .= '<option value="50"' . (($this->_options['rows'] == 50) ? ' selected="selected"' : '') . '>50</option>';
		$html .= '<option value="100"' . (($this->_options['rows'] == 100) ? ' selected="selected"' : '') . '>100</option>';
		$html .= '<option value="1000"' . (($this->_options['rows'] == 1000) ? ' selected="selected"' : '') . '>1000</option>';
		$html .= '</select>';
		$html .= '</th></tr>';
		return $html;
	}

	/**
	 * Renderuje nagłówki tabeli
	 * @return string
	 */
	public function renderHead() {
		if (!$this->_renderStarted) {
			$this->_setDefaultData();
			$this->_setDefaultColumns();
			$this->_renderStarted = true;
		}
		$html = '<tr>';
		foreach ($this->_columns as $column) {
			$sort = '';
			if (isset($this->_options['order'][$column['name']])) {
				$sort = ' ' . strtolower($this->_options['order'][$column['name']]);
			}
			if ($this->_view->getTranslate() !== null && $column['translate']) {
				if ($column['translate'] && $column['label'] != '' && $column['label'] != '#') {
					$column['label'] = $this->_view->getTranslate()->_($column['label']);
				}
			}
			if (isset($column['sort']) && $column['sort']) {
				$name = $column['sort'];
			} else {
				$name = $column['name'];
			}
			$id = 'id="' . $this->_id . '-order-' . $name . '"';
			$html .= '<th class="' . $column['class'] . '">';
			$html .= '<div>';
			if ($column['sortable'] || (isset($column['sort']) && $column['sort'])) {
				$icon = '';
				if ($sort == ' asc') {
					$icon = ' <i class="icon-upload"></i> ';
				} elseif ($sort == ' desc') {
					$icon = ' <i class="icon-download"></i> ';
				}
				$html .= '<a href="#' . $column['name'] . '" class="grid-spot' . $sort . '" ' . $id . '>' . $column['label'] . '</a>' . $icon;
			} else {
				$html .= $column['label'];
			}
			$html .= '</div>';
			if ($column['seekable']) {
				$html .= $this->_buildHeadInput($column);
			}
			$html .= '</th>';
		}
		$html .= '</tr>';
		return $html;
	}

	/**
	 * Renderuje pole filtra w nagłówku dla danej kolumny
	 * @param array $column tabela z własnościami kolumny
	 * @return string
	 */
	protected function _buildHeadInput(array $column) {
		$html = '';
		$value = '';
		if (isset($this->_options['filter'][$column['name']])) {
			$value = (string) $this->_options['filter'][$column['name']];
		}
		$selected = '';

		$id = 'id="' . $this->_id . '-filter-' . $column['name'] . '"';
		switch ($column['type']) {
			case 'text':
			case 'custom':
				if (isset($column['multiOptions']) && is_array($column['multiOptions'])) {
					$html = '<select class="grid-spot" ' . $id . '>';
					$html .= '<option value="">---</option>';
					foreach ($column['multiOptions'] as $optionValue => $optionName) {
						($value !== (string) $optionValue) ? $selected = '' : $selected = ' selected="selected"';
						$html .= '<option value="' . $optionValue . '"' . $selected . '>' . $optionName . '</option>';
					}
					$html .= '</select>';
				} else {
					$html = '<input class="grid-spot" type="text" ' . $id . ' value="' . htmlentities($value) . '"/>';
				}
				break;
			case 'checkbox':
				$on = self::ON;
				$off = self::OFF;
				if ($this->_view->getTranslate() !== null) {
					$on = $this->_view->getTranslate()->_($on);
					$off = $this->_view->getTranslate()->_($off);
				}
				$html = '<select class="grid-spot" ' . $id . '>';
				$html .= '<option value="">---</option>';
				($value !== '1') ? $selected = '' : $selected = ' selected="selected"';
				$html .= '<option value="1"' . $selected . '>' . $on . '</option>';
				($value !== '0') ? $selected = '' : $selected = ' selected="selected"';
				$html .= '<option value="0"' . $selected . '>' . $off . '</option>';
				$html .= '</select>';
				break;
			case 'counter':
				break;
			case 'select':
				if (isset($column['multiOptions']) && is_array($column['multiOptions'])) {
					$html = '<select class="grid-spot" ' . $id . '>';
					if ($column['type'] == 'select') {
						$html .= '<option value="">---</option>';
					}
					foreach ($column['multiOptions'] as $optionValue => $optionName) {
						($value !== (string) $optionValue) ? $selected = '' : $selected = ' selected="selected"';
						$html .= '<option value="' . $optionValue . '"' . $selected . '>' . $optionName . '</option>';
					}
					$html .= '</select>';
				}
				break;
		}
		return $html;
	}

	/**
	 * Renderuje kolumny
	 * @param \Mmi\Orm\Collection $rowData kolekcja modeli reprezentujących wiersze
	 * @param int $counter licznik
	 * @return string
	 */
	protected function _buildColumns($rowData, $counter) {
		$html = '<tr class="unhover" value="'. $rowData->id .'">';

		foreach ($this->_columns as $column) {
			$columnName = $column['name'];
			if (strpos($columnName, ':') !== false) {
				$path = explode(':', $columnName);
				$fieldData = '???';
				switch (count($path)) {
					case 2:
						$fieldData = $rowData->getJoined($path[0])->$path[1];
						break;
					case 3:
						$fieldData = $rowData->getJoined($path[1])->$path[2];
						break;
					case 4:
						$fieldData = $rowData->getJoined($path[2])->$path[3];
						break;
					case 5:
						$fieldData = $rowData->getJoined($path[3])->$path[4];
						break;
				}
			} else {
				$fieldData = property_exists($rowData, $column['name']) ? $rowData->$column['name'] : null;
			}
			$filters = isset($column['filters']) ? $column['filters'] : [];
			$html .= '<td class="' . $column['type'] . '">';
			if ($column['type'] != 'buttons' && $column['type'] != 'counter') {
				$id = 'id="' . $this->_id . '-field-' . $column['type'] . '-' . $column['name'] . '-' . $rowData->id . '"';
				switch ($column['type']) {
					case 'text':
						$data = $this->_applyFilters($filters, strip_tags($fieldData));
						if (strlen($data) > 128) {
							$data = mb_substr($data, 0, 125, 'utf-8') . '...';
						}
						if ($column['writeable'] && !$this->_options['locked']) {
							$html .= '<a href="#" ' . $id . ' class="grid-field-trigger">' . ($data ? $data : '&nbsp;') . '</a>';
						} else {
							$html .= '<div>' . $data . '</div>';
						}
						break;
					case 'image':
						$image = $fieldData;
						if ($image instanceof \Cms\Orm\File\Record) {
							$column['scale'] = (isset($column['scale']) && intval(isset($column['scale'])) > 0) ? $column['scale'] : 200;
							$column['scaleType'] = (isset($column['scaleType'])) ? $column['scaleType'] : 'scalex';
							$html .= '<div><img class="image" src="' . $this->_view->thumb($image, $column['scaleType'], $column['scale']) . '" alt="' . $column['name'] . '" /></div>';
						}
						break;
					case 'select':
						if ($column['writeable'] && !$this->_options['locked']) {
							$html .= '<select ' . $id . ' class="grid-field">';
							foreach ($column['multiOptions'] as $key => $value) {
								if ($key == $fieldData) {
									$selected = ' selected="selected"';
								} else {
									$selected = '';
								}
								$html .= '<option value="' . $key . '"' . $selected . '>' . $value . '</option>';
							}
							$html .= '</select>';
						} else {
							if (isset($column['multiOptions']['false'])) {
								$column['multiOptions'][0] = $column['multiOptions']['false'];
							}
							if (isset($column['multiOptions']['true'])) {
								$column['multiOptions'][1] = $column['multiOptions']['true'];
							}
							$value = isset($column['multiOptions'][$fieldData]) ? $column['multiOptions'][$fieldData] : $fieldData;
							$html .= '<div>' . $value . '</div>';
						}
						break;
					case 'checkbox':
						$checked = $fieldData ? ' checked=""' : '';
						if ($column['writeable'] && !$this->_options['locked']) {
							$html .= '<input type="checkbox" ' . $id . ' value="1" class="grid-field"' . $checked . '>';
						} else {
							$html .= '<input disabled="disabled" type="checkbox" ' . $id . $checked . '>';
						}
						break;
					case 'custom':
						$GLOBALS['rowData'] = $rowData;
						$column['value'] = preg_replace_callback('/%([a-zA-Z0-9_]+)%/', create_function(
								'$matches', 'return $GLOBALS[\'rowData\']->$matches[1];'
							), $column['value']);
						$this->_view->rowData = $rowData;
						$this->_view->column = $column;
						$html .= $this->_view->renderDirectly($column['value']);
						break;
				}
			} elseif ($column['type'] == 'buttons') {
				$value = '';
				if (isset($column['links']) && is_array($column['links']) && array_key_exists('edit', $column['links'])) {
					$linkEdit = $column['links']['edit'];
				} else {
					$linkEdit = $this->_options['links']['edit'];
				}
				if (null !== $linkEdit) {
					$value .= '<a href="' . $linkEdit . '"><i class="icon-pencil"></i></a>';
				}
				if (isset($column['links']) && is_array($column['links']) && array_key_exists('delete', $column['links'])) {
					$linkDelete = $column['links']['delete'];
				} else {
					$linkDelete = $this->_options['links']['delete'];
				}

				if (null !== $linkDelete) {
					$confirmDelete = self::DELETE;
					if ($this->_view->getTranslate() !== null) {
						$confirmDelete = $this->_view->getTranslate()->_(self::DELETE);
					}
					$value .= ' <a title="' . $confirmDelete . '" class="confirm" href="' . $linkDelete . '"><i class="icon-remove-circle"></i></a>';
				}
				foreach ($rowData->toArray() as $fieldName => $fieldValue) {
					if (is_string($fieldValue) || is_int($fieldValue)) {
						$value = str_replace('%25' . $fieldName . '%25', $fieldValue, $value);
					}
				}
				$html .= $value;
			} elseif ($column['type'] == 'counter') {
				$html .= '<div>' . $counter . '</div>';
			}
			$html .= '</td>';
		}
		$html .= '</tr>';
		return $html;
	}

	/**
	 * Ustawia domyślne opcje
	 */
	protected function _setDefaultOptions() {
		$this->_columns = [];
		$options = new \Mmi\Session\Space(get_class($this));
		$sessionOptions = $options->options;
		if (!empty($sessionOptions)) {
			$this->_options = $sessionOptions;
		} else {
			$this->_options = [
				'className' => get_class($this),
				'class' => 'grid',
				'locked' => true,
				'filter' => [],
				'order' => [],
				'pagination' => true,
				'page' => '1',
				'rows' => '20',
			];
		}
		$this->_options['links'] = [
			'edit' => $this->_view->url(['id' => '%id%', 'action' => 'edit', 'baseModule' => null, 'baseController' => null]),
			'delete' => $this->_view->url(['id' => '%id%', 'action' => 'delete', 'baseModule' => null, 'baseController' => null]),
		];
	}

	/**
	 * Ustawia dane dla grid'a
	 */
	protected function _setDefaultData() {
		$queryClass = get_class($this->_daoQuery);
		//nakładanie filtrów na query
		foreach ($this->_options['filter'] as $field => $value) {
			$subQ = $queryClass::factory();
			$type = 'text';
			foreach ($this->_columns as $column) {
				if ($column['name'] == $field) {
					$type = $column['type'];
					break;
				}
			}
			$table = null;
			if (strpos($field, ':') !== false) {
				$fld = explode(':', $field);
				if (count($fld) != 2) {
					continue;
				}
				$table = $fld[0];
				$field = $fld[1];
			}
			if (($type == 'select' || $type == 'checkbox') && !isset($column['like'])) {
				$this->_daoQuery->andField($field, $table)->equals($value);
			} else {
				$this->_daoQuery->andField($field, $table)->like($value . '%');
			}
		}
		//nakładanie sortów
		if (!empty($this->_options['order'])) {
			$this->_daoQuery->resetOrder();
		}
		foreach ($this->_options['order'] as $field => $value) {
			$table = null;
			if (strpos($field, ':') !== false) {
				$fld = explode(':', $field);
				if (count($fld) != 2) {
					continue;
				}
				$table = $fld[0];
				$field = $fld[1];
			}
			if ($value == 'ASC') {
				$this->_daoQuery->orderAsc($field, $table);
			} else {
				$this->_daoQuery->orderDesc($field, $table);
			}
		}
		//jak wlaczona paginacja to limity i offset
		if ($this->_options['pagination'] === true) {
			//nakładanie limitu
			$this->_daoQuery->limit($this->_options['rows']);
			//nakładanie offsetu
			$this->_daoQuery->offset(($this->_options['page'] - 1) * $this->_options['rows']);
		}
		$this->_dataCount = $this->_daoQuery->count();
		$this->_data = $this->_daoQuery->find();
	}

	/**
	 * Ustawia obowiązkowe kolumny (licznik)
	 */
	protected function _setDefaultColumns() {
		$this->addColumn('counter', 'counter', ['append' => false]);
	}

	/**
	 * Zwraca ilość stron
	 * @return int
	 */
	protected function _getPagesCount() {
		$pages = [];
		for ($i = 1; $i <= ceil($this->_dataCount / $this->_options['rows']); $i++) {
			$pages[$i] = $i;
		}
		return $pages;
	}

	/**
	 * Zastosowanie filtrów na danaj zmiennej
	 * @param array $filters lista filtrów do zaaplikowania
	 * @param string $value wartość do przerobienia
	 * @return string
	 */
	protected function _applyFilters($filters, $value) {
		if (empty($filters)) {
			return $value;
		}
		foreach ($filters as $filter) {
			$params = explode(':', $filter);
			$filterName = $params[0];
			array_shift($params);
			$filter = $this->_getFilter($filterName);
			if (!empty($params)) {
				$filter->setOptions($params);
			}
			$value = $filter->filter($value);
		}
		return $value;
	}

	/**
	 * Pobiera obiekt filtra
	 * @param string $name nazwa filtra
	 * @return \Mmi\Filter\Interface
	 */
	protected final function _getFilter($name) {
		$name = ucfirst($name);
		$className = '\\Mmi\\Filter\\' . $name;
		if (isset($this->_filters[$className])) {
			return $this->_filters[$className];
		}
		$filter = new $className();
		$this->_filters[$className] = $filter;
		return $filter;
	}

}
