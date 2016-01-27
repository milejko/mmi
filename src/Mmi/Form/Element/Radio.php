<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Element radiobutton
 * 
 * Metody add
 * @method self addClass($className) dodaje klasę HTML
 * @method self addFilter(\Mmi\Filter\FilterAbstract $filter) dodaje filtr
 * @method self addValidator(\Mmi\Validator\ValidatorAbstract $validator) dodaje walidator
 * @method self addError($error) dodaje błąd
 * 
 * Settery
 * @method self setName($name) ustawia nazwę
 * @method self setValue($value) ustawia wartość
 * @method self setId($id) ustawia identyfikator
 * @method self setPlaceholder($placeholder) ustawia placeholder pola
 * @method self setDescription($description) ustawia opis
 * @method self setIgnore($ignore = true) ustawia ignorowanie
 * @method self setDisabled($disabled = true) ustawia wyłączone
 * @method self setReadOnly($readOnly = true) ustawia tylko do odczytu
 * @method self setLabel($label) ustawia labelkę
 * @method self setRequiredAsterisk($asterisk = '*') ustawia znak gwiazdki
 * @method self setRequired($required = true) ustawia wymagalność
 * @method self setLabelPostfix($labelPostfix) ustawia postfix labelki
 * @method self setForm(\Mmi\Form\Form $form) ustawia formularz
 * 
 * @method self setMultioptions(array $multioptions = []) ustawia multiopcje
 * 
 * Gettery
 * @method array getMultioptions() pobiera multiopcje
 * 
 * Walidatory
 * @method self addValidatorAlnum($message = null) walidator alfanumeryczny
 * @method self addValidatorDate($message = null) walidator daty
 * @method self addValidatorEmailAddress($message = null) walidator email
 * @method self addValidatorEmailAddressList($message = null) walidator listy email
 * @method self addValidatorEqual($value, $message = null) walidator równości
 * @method self addValidatorIban($country = null, $message = null) walidator IBAN
 * @method self addValidatorInteger($message = null) walidator liczb całkowitych
 * @method self addValidatorIp4($message = null) walidator IPv4
 * @method self addValidatorIp6($message = null) walidator IPv6
 * @method self addValidatorNotEmpty($message = null) walidator niepustości
 * @method self addValidatorNumberBetween($from, $to, $message = null) walidator numer pomiędzy
 * @method self addValidatorNumeric($message = null) walidator numeryczny
 * @method self addValidatorPostal($message = null) walidator kodu pocztowego
 * @method self addValidatorRecordUnique(\Mmi\Orm\Query $query, $field, $id = null, $message = null) walidator unikalności rekordu
 * @method self addValidatorRegex($pattern, $message = null) walidator regex
 * @method self addValidatorStringLength($message = null) walidator długości ciągu
 * 
 * Filtry
 * @method self addFilterAlnum() filtr alfanumeryczny
 * @method self addFilterAscii() filtr ASCII
 * @method self addFilterCapitalize() filtr kapitalizacja
 * @method self addFilterCeil() filtr sufit
 * @method self addFilterCount() filtr zliczający
 * @method self addFilterDateFormat($format) filtr formatujący datę
 * @method self addFilterDump() filtr zrzucający
 * @method self addFilterEmptyToNull() filtr konwertujący pustą wartość do null
 * @method self addFilterEscape() filtr wykluczający HTML
 * @method self addFilterInput() filtr tekstowy
 * @method self addFilterIntval() filtr konwertujący do liczby całkowitej
 * @method self addFilterIsEmpty() filtr sprawdzający pustość
 * @method self addFilterLength() filtr długości zmiennej
 * @method self addFilterLowercase() filtr obniżający litery
 * @method self addFilterMarkupProperty() filtr wycina znaki do poprawnych dla atrybutu HTML
 * @method self addFilterNl2Br() filtr nowa linia do br
 * @method self addFilterNumberFormat($digits, $separator, $thousands = null, $trimZeros = null, $trimZerosLeave = null) filtr format liczby
 * @method self addFilterReplace($search, $replace) filtr zamiana
 * @method self addFilterRound($precision) filtr zaokrąglenie z precyzją
 * @method self addFilterStringTrim($extras) filtr trim
 * @method self addFilterStripTags($exceptions) filtr usuwanie tagów HTML
 * @method self addFilterTinyMce() filtr dla tinyMce
 * @method self addFilterTruncate($length, $ending = '...', $boundary = false) filtr obcięcie
 * @method self addFilterUppercase() filtr wielkie litery
 * @method self addFilterUrl() filtr url
 * @method self addFilterUrlencode() filtr urlencode
 * @method self addFilterZeroToNull() filtr zero do null'a
 */
class Radio extends ElementAbstract {

	/**
	 * Buduje pole
	 * @return string
	 */
	public function fetchField() {
		$baseId = $this->getId();
		$value = $this->getValue();
		$html = '<ul id="' . $this->getId() . '-list">';
		$f = new \Mmi\Filter\Url;
		foreach ($this->getMultioptions() as $key => $caption) {
			//konfiguracja pola
			$this->setValue($key)
				->unsetOption('checked')
				->setId($baseId . '-' . $f->filter($key));
			//ustalenie zaznaczenia
			if ($value !== null && $value == $key) {
				$this->setOption('checked', '');
			}
			//wartość wyłączona
			if (strpos($key, ':disabled') !== false) {
				$this->setDisabled();
			}
			$html .= '<li id="' . $this->getId() . '-item">
				<input type="radio" ' . $this->_getHtmlOptions() . ' />
				<label for="' . $this->getId() . '">' . $caption . '</label></li>';
		}
		//reset całego pola
		$this->setId($baseId)
			->setValue($value);
		return $html . '</ul>';
	}

	/**
	 * Buduje etykietę pola
	 * @return string
	 */
	public function fetchLabel() {
		//brak labelki
		if (!isset($this->_options['label'])) {
			return;
		}
		$requiredClass = '';
		$required = '';
		//html znaku wymagania
		if ($this->getRequired()) {
			$requiredClass = ' class="required"';
			$required = '<span class="required">' . $this->getOption('data-requiredAsterisk') . '</span>';
		}
		//tłumaczenie labelki
		$label = $this->_options['label'];
		if ($this->_translatorEnabled) {
			$label = $this->getTranslate()->_($label);
		}
		//rendering
		return '<label' . $requiredClass . '>' . $label . $this->_options['labelPostfix'] . $required . '</label>';
	}

}
