<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

abstract class ElementAbstract extends Base\Element {

	/**
	 * Automatyczne tłumaczenie opisów i etykiet pól
	 * @var boolean
	 */
	protected $_translatorEnabled = true;

	/**
	 * Błędy pola
	 * @var array
	 */
	protected $_errors = [];

	/**
	 * Formularz macierzysty
	 * @var \Mmi\Form\Form
	 */
	protected $_form = null;

	/**
	 * Konstruktor, ustawia nazwę pola i opcje
	 * @param string $name nazwa
	 * @param array $options opcje
	 */
	public function __construct($name) {
		$this->setName($name)
			->setRequired(false)
			->setRequiredAsterisk('*')
			->setMarkRequired()
			->setLabelPostfix(':')
			->setIgnore(false)
			->setDisableTranslator()
			->init();
	}

	/**
	 * Ustawia form macierzysty
	 * @param \Mmi\Form\Form $form
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setForm(\Mmi\Form\Form $form) {
		$this->_form = $form;
		return $this;
	}

	/**
	 * Pobranie formularza macierzystego
	 * @return \Mmi\Form\Form
	 */
	public final function getForm() {
		return $this->_form;
	}

	/**
	 * Wyłącza translator
	 * @param boolean $disable
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setDisableTranslator($disable = true) {
		$this->_translatorEnabled = !$disable;
		return $this;
	}

	/**
	 * Pobiera translator
	 * @return \Mmi\Translate
	 */
	public final function getTranslate() {
		$translate = \Mmi\App\FrontController::getInstance()->getView()->getTranslate();
		return (null === $translate) ? new \Mmi\Translate() : $translate;
	}

	/**
	 * Ustawia wartość pola formularza
	 * @param mixed $value wartość
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setValue($value) {
		return $this->setOption('value', $this->_applyFilters($value));
	}

	/**
	 * Waliduje pole
	 * @return boolean
	 */
	public final function isValid() {
		$result = true;
		//waliduje poprawnie jeśli niewymagane, ale tylko gdy niepuste
		if (!($this->isRequired() || $this->getValue() != '')) {
			return true;
		}
		foreach ($this->getValidators() as $validator) {
			$options = [];
			$message = null;
			if (is_array($validator)) {
				$options = isset($validator['options']) ? $validator['options'] : [];
				$message = isset($validator['message']) ? $validator['message'] : null;
				$validator = $validator['validator'];
			}
			$v = $this->_getValidator($validator);
			$v->setOptions($options);
			if (!$v->isValid($this->getValue())) {
				$result = false;
				$this->_errors[] = ($message !== null) ? $message : $v->getError();
			}
		}
		return $result;
	}

	/**
	 * Zwraca czy pole ma błędy
	 * @return boolean
	 */
	public final function hasErrors() {
		return !empty($this->_errors);
	}

	/**
	 * Pobiera błędy pola
	 * @return array
	 */
	public final function getErrors() {
		return $this->_errors;
	}

	/**
	 * Dodaje błąd
	 * @param string $error
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function addError($error) {
		$this->_errors[] = $error;
		return $this;
	}

	/**
	 * Pobiera obiekt filtra
	 * @param string $name nazwa filtra
	 * @return \Mmi\Filter\FilterAbstract
	 */
	protected final function _getFilter($name) {
		$structure = \Mmi\App\FrontController::getInstance()->getStructure('filter');
		foreach ($structure as $namespace => $filters) {
			if (!isset($filters[$name])) {
				continue;
			}
			$className = '\\' . $namespace . '\\Filter\\' . ucfirst($name);
		}
		if (!isset($className)) {
			throw new \Mmi\Form\FormException('Unknown filter: ' . $name);
		}
		return new $className();
	}

	/**
	 * Pobiera nazwę walidatora
	 * @param string $name nazwa walidatora
	 * @return \Mmi\Validator\ValidatorAbstract
	 */
	protected final function _getValidator($name) {
		$structure = \Mmi\App\FrontController::getInstance()->getStructure('validator');
		foreach ($structure as $namespace => $validators) {
			if (!isset($validators[$name])) {
				continue;
			}
			$className = '\\' . $namespace . '\\Validator\\' . ucfirst($name);
		}
		if (!isset($className)) {
			throw new \Mmi\Form\FormException('Unknown validator: ' . $name);
		}
		return new $className();
	}

	/**
	 * Filtruje daną wartość za pomocą filtrów pola
	 * @param mixed $value wartość
	 * @return mixed wynik filtracji
	 */
	protected function _applyFilters($value) {
		//iteracja po filtrach
		foreach ($this->getFilters() as $filter) {
			//pobranie filtra, ustawienie opcji i filtracja zmiennej
			$value = $this->_getFilter($filter['filter'])
				->setOptions(isset($filter['options']) ? $filter['options'] : [])
				->filter($value);
		}
		return $value;
	}

}
