<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Abstrakcyjna klasa elementu formularza
 * @method self setName($name) ustawia nazwę
 * @method string getName() pobiera nazwę
 * @method mixed getValue() pobiera wartość pola
 * @method ElementAbstract setId($id) ustawia identyfikator
 * @method string getId() pobiera nazwę
 * @method ElementAbstract setPlaceholder($placeholder) ustawia placeholder pola
 * @method string getPlaceholder() pobiera placeholder
 * 
 * @method self addValidatorAlnum() walidator alfanumeryczny
 * @method self addValidatorDate() walidator daty
 * @method self addValidatorEmailAddress() walidator email
 * @method self addValidatorEmailAddressList() walidator listy email
 * @method self addValidatorEqual($value) walidator równości
 * @method self addValidatorIban($country = null) walidator IBAN
 * @method self addValidatorInteger() walidator liczb całkowitych
 * @method self addValidatorIp4() walidator IPv4
 * @method self addValidatorIp6() walidator IPv6
 * @method self addValidatorNotEmpty() walidator niepustości
 * @method self addValidatorNumberBetween($from, $to) walidator numer pomiędzy
 * @method self addValidatorNumeric() walidator numeryczny
 * @method self addValidatorPostal() walidator kodu pocztowego
 * @method self addValidatorRecordUnique(\Mmi\Orm\Query $query, $field, $id = null) walidator unikalności rekordu
 * @method self addValidatorRegex($pattern) walidator regex
 * @method self addValidatorStringLength() walidator numeryczny
 */
abstract class ElementAbstract extends \Mmi\OptionObject {

	/**
	 * Błędy pola
	 * @var array
	 */
	protected $_errors = [];
	
	/**
	 * Tablica walidatorów
	 * @var \Mmi\Validator\ValidatorAbstract[]
	 */
	protected $_validators = [];

	/**
	 * Formularz macierzysty
	 * @var \Mmi\Form\Form
	 */
	protected $_form = null;

	/**
	 * Konstruktor
	 * @param string $name nazwa
	 */
	public function __construct($name) {
		//ustawia nazwę i opcje domyślne
		$this->setName($name)
			->setRequired(false)
			->setRequiredAsterisk('*')
			->setLabelPostfix(':')
			->setIgnore(false)
			//dodaje klasę HTML (field)
			->addClass('field');
	}

	/**
	 * Dodaje klasę do elementu
	 * @param string $className nazwa klasy
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function addClass($className) {
		return $this->setOption('class', trim($this->getOption('class') . ' ' . $className));
	}

	/**
	 * Ustawia wartość pola formularza
	 * @param mixed $value wartość
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setValue($value) {
		//ustawia filtrowaną wartość
		return $this->setOption('value', $this->_applyFilters($value));
	}

	/**
	 * Ustawia opis
	 * @param string $description
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setDescription($description) {
		return $this->setOption('data-description', $description);
	}

	/**
	 * Pobiera opis
	 * @return string
	 */
	public final function getDescription() {
		return $this->getOption('data-description');
	}

	/**
	 * Ustawia ignorowanie pola
	 * @param bool $ignore
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setIgnore($ignore = true) {
		return $this->setOption('data-ignore', (bool) $ignore);
	}

	/**
	 * Zwraca czy pole jest ignorowane
	 * @return boolean
	 */
	public final function getIgnore() {
		return (bool) $this->getOption('data-ignore');
	}

	/**
	 * Ustawia wyłączenie pola
	 * @param bool $disabled
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setDisabled($disabled = true) {
		return $disabled ? $this->setOption('disabled', '') : $this;
	}

	/**
	 * Zwraca czy pole jest wyłączone
	 * @return boolean
	 */
	public final function getDisabled() {
		return null !== $this->getOption('disabled');
	}

	/**
	 * Ustawia pole do odczytu
	 * @param boolean $readOnly
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setReadOnly($readOnly = true) {
		return $readOnly ? $this->setOption('readonly', '') : $this;
	}

	/**
	 * Ustawia label pola
	 * @param string $label
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setLabel($label) {
		return $this->setOption('data-label', $label);
	}

	/**
	 * Pobiera label
	 * @return string
	 */
	public final function getLabel() {
		return $this->getOption('data-label');
	}

	/**
	 * Ustawia postfix labela
	 * @param string $labelPostfix postfix labelki
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setLabelPostfix($labelPostfix) {
		return $this->setOption('data-labelPostfix', $labelPostfix);
	}

	/**
	 * Pobiera postfix labelki
	 * @return string
	 */
	public final function getLabelPostfix() {
		return $this->getOption('data-labelPostfix');
	}

	/**
	 * Ustawia czy pole jest wymagane
	 * @param bool $required wymagane
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setRequired($required = true) {
		return $this->setOption('data-required', (bool) $required);
	}

	/**
	 * Zwraca czy pole jest wymagane
	 * @return boolean
	 */
	public final function getRequired() {
		return (bool) $this->getOption('data-required');
	}

	/**
	 * Ustawia symbol gwiazdki pól wymaganych
	 * @param string $asterisk
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setRequiredAsterisk($asterisk = '*') {
		return $this->setOption('data-requiredAsterisk', $asterisk);
	}

	/**
	 * Ustawia wszystkie opcje wyboru na podstawie tabeli
	 * @param array $multiOptions opcje
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setMultiOptions(array $multiOptions = []) {
		return $this->setOption('multiOptions', $multiOptions);
	}

	/**
	 * Zwraca multi opcje pola
	 * @return array
	 */
	public final function getMultiOptions() {
		return is_array($this->getOption('multiOptions')) ? $this->getOption('multiOptions') : [];
	}

	/**
	 * Dodaje opcję wyboru
	 * @param string $value wartość
	 * @param string $caption nazwa
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function addMultiOption($value, $caption) {
		$multiOptions = $this->getMultiOptions();
		$multiOptions[$value] = $caption;
		return $this->setOption('multiOptions', $multiOptions);
	}

	/**
	 * Dodaje walidator
	 * @param string $name nazwa
	 * @param string $options opcje
	 * @param string $message wiadomość
	 * @return self
	 */
	public final function addValidator(\Mmi\Validator\ValidatorAbstract $validator) {
		//ustawianie opcji na elemencie
		$this->_validators[get_class($validator)] = $validator;
		return $this;
	}

	/**
	 * Pobiera walidatory
	 * @return \Mmi\Validator\ValidatorAbstract[]
	 */
	public final function getValidators() {
		return is_array($this->_validators) ? $this->_validators : [];
	}

	/**
	 * Dodaje filtr
	 * @param string $name nazwa
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function addFilter($name, array $options = []) {
		$filters = $this->getFilters();
		$filters[] = ['filter' => $name, 'options' => $options];
		return $this->setOption('filters', $filters);
	}

	/**
	 * Pobiera walidatory
	 * @return \Mmi\Filter\FilterAbstract[]
	 */
	public final function getFilters() {
		return is_array($this->getOption('filters')) ? $this->getOption('filters') : [];
	}

	/**
	 * Ustawia form macierzysty
	 * @param \Mmi\Form\Form $form
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setForm(\Mmi\Form\Form $form) {
		$this->_form = $form;
		//ustawianie ID
		$this->setId($form->getBaseName() . '-' . $this->getName());
		return $this;
	}

	/**
	 * Waliduje pole
	 * @return boolean
	 */
	public final function isValid() {
		$result = true;
		//waliduje poprawnie jeśli niewymagane, ale tylko gdy niepuste
		if (!($this->getRequired() || $this->getValue() != '')) {
			return $result;
		}
		//iteracja po walidatorach
		foreach ($this->getValidators() as $validator) {
			if ($validator->isValid($this->getValue())) {
				continue;
			}
			$result = false;
			//@TODO: custom error messages	
			$this->addError($validator->getError());
		}
		//zwrot rezultatu wszystkich walidacji (iloczyn)
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
		return \Mmi\App\FrontController::getInstance()->getView()->getFilter($name);
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

	/**
	 * Buduje opcje HTML
	 * @return string
	 */
	protected final function _getHtmlOptions() {
		//jeśli istnieją validatory dodajemy klasę validate
		if (!empty($this->getValidators())) {
			$this->addClass('validate');
		}
		$html = '';
		//iteracja po opcjach do HTML
		foreach ($this->getOptions() as $key => $value) {
			//ignorowanie niemożliwych do wypisania
			if (!is_string($value) && !is_numeric($value)) {
				continue;
			}
			$html .= $key . '="' . str_replace('"', '&quot;', $value) . '" ';
		}
		//zwrot html
		return $html;
	}

	/**
	 * Kolejność renderowania pola
	 * @var array
	 */
	protected $_renderingOrder = ['fetchBegin', 'fetchLabel', 'fetchField', 'fetchDescription', 'fetchErrors', 'fetchEnd'];

	/**
	 * Renderer pola
	 * @return string
	 */
	public function __toString() {
		try {
			$html = '';
			//ustawienie nazwy po nazwie forma
			if ($this->_form) {
				$this->setName($this->_form->getBaseName() . '[' . rtrim($this->getName(), '[]') . ']' . (substr($this->getName(), -2) == '[]' ? '[]' : ''));
			}
			foreach ($this->_renderingOrder as $method) {
				if (!method_exists($this, $method)) {
					continue;
				}
				$html .= $this->{$method}();
			}
			return $html;
		} catch (\Exception $e) {
			return $e->getMessage();
		}
	}

	/**
	 * Ustaw kolejność realizacji
	 * @param array $renderingOrder
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setRenderingOrder(array $renderingOrder = []) {
		foreach ($renderingOrder as $method) {
			if (!method_exists($this, $method)) {
				throw new \Mmi\Form\FormException('Unknown rendering method');
			}
		}
		$this->_renderingOrder = $renderingOrder;
		return $this;
	}

	/**
	 * Buduje kontener pola (początek)
	 * @return string
	 */
	public final function fetchBegin() {
		$class = get_class($this);
		$this->addClass(strtolower(substr($class, strrpos($class, '\\') + 1)));
		if ($this->hasErrors()) {
			$this->addClass('error');
		}
		return '<div' . ($this->getId() ? ' id="' . $this->getId() . '-container"' : '') . ' class="' . $this->getOption('class') . '">';
	}

	/**
	 * Buduje kontener pola (koniec)
	 * @return string
	 */
	public final function fetchEnd() {
		return '<div class="clear"></div></div>';
	}

	/**
	 * Buduje etykietę pola
	 * @return string
	 */
	public function fetchLabel() {
		if (!$this->getLabel()) {
			return;
		}
		$forHtml = $this->getId() ? ' for="' . $this->getId() . '" id="' . $this->getId() . '-label"' : '';
		$requiredClass = '';
		$required = '';
		if ($this->getRequired()) {
			$requiredClass = ' class="required"';
			$required = '<span class="required">' . $this->getOption('data-requiredAsterisk') . '</span>';
		}
		//zwrot html
		return '<label' . $forHtml . $requiredClass . '>' . $this->getLabel() . $this->getOption('data-labelPostfix') . $required . '</label>';
	}

	/**
	 * Buduje pole
	 * @return string
	 */
	public abstract function fetchField();

	/**
	 * Buduje opis pola
	 * @return string
	 */
	public final function fetchDescription() {
		//brak opisu
		if (!$this->getDescription()) {
			return;
		}
		$id = $this->getId() ? ' id="' . $this->getId() . '-description"' : '';
		$description = $this->getDescription();
		return '<div' . $id . ' class="description">' . $description . '</div>';
	}

	/**
	 * Buduje błędy pola
	 * @return string
	 */
	public final function fetchErrors() {
		$idHtml = $this->getId() ? ' id="' . $this->getId() . '-errors"' : '';
		$html = '<div class="errors"' . $idHtml . '>';
		if ($this->hasErrors()) {
			$html .= '<span class="marker"></span>'
				. '<ul>'
				. '<li class="point first"></li>';
			foreach ($this->_errors as $error) {
				$html .= '<li class="notice error"><i class="icon-remove-sign icon-large"></i>' . $error . '</li>';
			}
			$html .= '<li class="close last"></li>'
				. '</ul>';
		}
		$html .= '<div class="clear"></div></div>';
		return $html;
	}
	
	/**
	 * Obsługa dodawania validatorów i filtrów
	 * @param string $name
	 * @param array $params
	 * @return mixed
	 */
	public function __call($name, $params) {
		$matches = [];
		//obsługa getterów
		if (preg_match('/^addValidator([a-zA-Z0-9]+)/', $name, $matches)) {
			$validatorClass = '\\Mmi\\Validator\\' . $matches[1];
			return $this->addValidator(new $validatorClass($params));
		}
		return parent::__call($name, $params);
	}

}
