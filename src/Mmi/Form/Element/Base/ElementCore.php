<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element\Base;

abstract class ElementCore extends \Mmi\OptionObject {

	/**
	 * Funkcja użytkownika, jest wykonywana na końcu konstruktora
	 */
	public function init() {
		
	}

	/**
	 * Funkcja użytkownika, jest wykonywana przed renderingiem
	 */
	public function preRender() {
		
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
	 * Ustawia nazwę pola formularza
	 * @param mixed $name wartość
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setName($name) {
		return $this->setOption('name', $name);
	}

	/**
	 * Pobiera nazwę pola formularza
	 * @return string
	 */
	public final function getName() {
		return $this->getOption('name');
	}

	/**
	 * Pobiera wartość pola formularza
	 * @return mixed
	 */
	public final function getValue() {
		return $this->getOption('value');
	}

	/**
	 * Wyłącza ajax dla formularza
	 * @param bool $disable default: true
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setAjaxDisable($disable = true) {
		return $this->setOption('noAjax', (bool) $disable);
	}

	/**
	 * Ustawia opis
	 * @param string $description
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setDescription($description) {
		return $this->setOption('description', $description);
	}

	/**
	 * Ustawia placeholder (HTML5)
	 * @param string $placeholder
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setPlaceholder($placeholder) {
		return $this->setOption('placeholder', $placeholder);
	}

	/**
	 * Ustawia ignorowanie pola
	 * @param bool $ignore
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setIgnore($ignore = true) {
		return $this->setOption('ignore', (bool) $ignore);
	}

	/**
	 * Zwraca czy pole jest ignorowane
	 * @return boolean
	 */
	public final function isIgnored() {
		return (bool) $this->getOption('ignore');
	}

	/**
	 * Ustawia wyłączenie pola
	 * @param bool $disabled
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setDisabled($disabled = true) {
		if ($disabled) {
			return $this->setOption('disabled', '');
		}
		return $this;
	}

	/**
	 * Zwraca czy pole jest wyłączone
	 * @return boolean
	 */
	public final function isDisabled() {
		return $this->getOption('disabled') !== null;
	}

	/**
	 * Ustawia pole do odczytu
	 * @param readOnly $disable
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setReadOnly($readOnly = true) {
		if ($readOnly) {
			return $this->setOption('readonly', '');
		}
		return $this;
	}

	/**
	 * Ustawia label pola
	 * @param string $label
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setLabel($label) {
		return $this->setOption('label', $label);
	}

	/**
	 * Ustawia postfix labela
	 * @param string $labelPostfix postfix labelki
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setLabelPostfix($labelPostfix) {
		return $this->setOption('labelPostfix', $labelPostfix);
	}

	/**
	 * Ustawia wymagalność pola
	 * @param bool $markRequired oznacz wymagane
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setMarkRequired($markRequired = true) {
		return $this->setOption('markRequired', (bool) $markRequired);
	}

	/**
	 * Ustawia czy pole jest wymagane
	 * @param bool $required wymagane
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setRequired($required = true) {
		return $this->setOption('required', (bool) $required);
	}

	/**
	 * Zwraca czy pole jest wymagane
	 * @return boolean
	 */
	public final function isRequired() {
		return (bool) $this->getOption('required');
	}

	/**
	 * Ustawia symbol gwiazdki pól wymaganych
	 * @param string $symbol
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setRequiredAsterisk($symbol = '*') {
		return $this->setOption('requiredAsterisk', $symbol);
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
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function addValidator($name, array $options = [], $message = null) {
		$validators = $this->getValidators();
		$validator = ['validator' => $name, 'options' => $options];
		if ($message !== null) {
			$validator['message'] = $message;
		}
		$validators[] = $validator;
		return $this->setOption('validators', $validators);
	}

	/**
	 * Pobiera walidatory
	 * @return array
	 */
	public final function getValidators() {
		return is_array($this->getOption('validators')) ? $this->getOption('validators') : [];
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
	 * @return array
	 */
	public final function getFilters() {
		return is_array($this->getOption('filters')) ? $this->getOption('filters') : [];
	}

	/**
	 * Ustawia html użytkownika
	 * @param string $html
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function setCustomHtml($html) {
		return $this->setOption('customHtml', $html);
	}

}
