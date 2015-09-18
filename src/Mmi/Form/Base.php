<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form;

abstract class Base extends \Mmi\OptionObject {

	/**
	 * Elementy formularza
	 * @var array
	 */
	protected $_elements = [];

	/**
	 * Nazwa formularza
	 * @var string
	 */
	protected $_formBaseName;

	/**
	 * CTRL pochodzący z POST
	 * @var string
	 */
	protected $_ctrl;

	/**
	 * Obiekt rekordu
	 * @var \Mmi\Orm\Record
	 */
	protected $_record;

	/**
	 * Czy zapisany
	 * @var boolean
	 */
	protected $_saved = false;

	/**
	 * Dane prawidłowe
	 * @var boolean
	 */
	protected $_valid;

	/**
	 * Konstruktor
	 * @param \Mmi\Orm\Record $record obiekt recordu
	 * @param array $options opcje
	 */
	public function __construct(\Mmi\Orm\Record $record = null, array $options = []) {
		//ustawienia opcji
		$this->setOptions($options);
		//podłączenie rekordu
		$this->_record = $record;

		//kalkulacja nazwy bazowej formularza
		$this->_formBaseName = strtolower(str_replace('\\', '-', get_class($this)));

		//domyślne opcje
		$this->setOption('class', $this->_formBaseName . ' vertical')
			->setOption('accept-charset', 'utf-8')
			->setOption('method', 'post')
			->setOption('enctype', 'multipart/form-data');

		//inicjalizacja formularza
		$this->init();

		//dodawanie CTRL
		$this->addElementHidden($this->_formBaseName . '__ctrl')
			->setIgnore();

		//dane z rekordu
		$this->hasNotEmptyRecord() && $this->setFromRecord($this->_record);

		//dane z POST
		$this->isMine() && $this->setFromPost(\Mmi\Controller\Front::getInstance()->getRequest()->getPost());

		//zapis formularza
		$this->save();
	}

	/**
	 * Inicjalizacja formularza przez programistę końcowego
	 */
	abstract public function init();

	/**
	 * Metoda walidacji całego formularza (domyślnie zawsze przechodzi)
	 * @return boolean
	 */
	public function validator() {
		return true;
	}

	/**
	 * Ustawia akcję formularza
	 * @param string $value akcja
	 * @return \Mmi\Form
	 */
	public final function setAction($value) {
		return $this->setOption('action', $value);
	}

	/**
	 * Ustawia zabezpieczenie CSRF
	 * @param boolean $secured
	 * @return \Mmi\Form
	 */
	public final function setSecured($secured = true) {
		return $this->setOption('secured', (bool) $secured);
	}

	/**
	 * Dodawanie elementu formularza z gotowego obiektu
	 * @param \Mmi\Form\Element\ElementAbstract $element obiekt elementu formularza
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function addElement(\Mmi\Form\Element\ElementAbstract $element) {
		//ustawianie opcji na elemencie
		return $this->_elements[$element->getName()] = $element
			->setForm($this)
			->setOption('id', $this->_formBaseName . '-' . $element->getName())
			->setOption('class', trim('field ' . $element->getOption('class')));
	}

	/**
	 * Pobranie elementów formularza
	 * @return \Mmi\Form\Element\ElementAbstract[]
	 */
	public final function getElements() {
		return $this->_elements;
	}

	/**
	 * Pobranie elementu formularza
	 * @param string $name nazwa elementu
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function getElement($name) {
		return isset($this->_elements[$name]) ? $this->_elements[$name] : null;
	}

	/**
	 * Zwraca czy dane POST są przeznaczone dla tego formularza
	 * @return boolean
	 */
	public final function isMine() {
		//sprawdzenie istnienia w POST prawidłowo nazwanej zmiennej CTRL
		return \Mmi\Controller\Front::getInstance()
				->getRequest()
				->getPost()
				->__isset($this->_formBaseName . '__ctrl');
	}

	/**
	 * Walidacja formularza
	 * @return boolean
	 */
	public final function isValid() {
		//formularz już zwalidowany
		if (null !== $this->_valid) {
			return $this->_valid;
		}
		//dane nie od danego formularza
		if (!$this->isMine()) {
			return $this->_valid = false;
		}

		//odczytywanie danych CTRL
		$options = \Mmi\Convert\Table::fromString($this->_ctrl);

		//sprawdzenie zgodności klas z CTRL z bieżącą klasą
		if ($options['class'] != get_class($this)) {
			return $this->_valid = false;
		}
		//jeśli form zabezpieczony przed CSRF, sprawdzenie hash
		if ($this->getOption('secured') && $options['hash'] != \Mmi\Session\Space::factory('\Mmi\Form')->{$this->_formBaseName}) {
			return $this->_valid = false;
		}
		$validationResult = true;
		//walidacja poszczególnych elementów formularza
		foreach ($this->getElements() as $element) {
			//jeśli nieprawidłowy walidacja trwa dalej, ale wynik jest już negatywny
			if (!$element->isValid()) {
				$validationResult = false;
			}
		}
		//rezultat walidacji
		return $this->_valid = $validationResult && $this->validator();
	}

	/**
	 * Ustawia forma na podstawie obiektu POST
	 * @param \Mmi\Controller\Request\Post $post
	 * @return \Mmi\Form
	 */
	public final function setFromPost(\Mmi\Controller\Request\Post $post) {
		//dane z posta do tablicy
		$data = $post->toArray();
		//sprawdzenie wartości dla wszystkich elementów
		foreach ($this->getElements() as $element) {
			//wyłączone nie są zapisywane z POST
			if ($element->isDisabled()) {
				continue;
			}
			$keyExists = array_key_exists($element->getName(), $data);
			//selecty multiple i serie checkboxów dostają pusty array jeśli:
			//brak wartości oraz dane z POST
			if (($element instanceof \Mmi\Form\Element\MultiCheckbox || ($element instanceof \Mmi\Form\Element\Select && $element->getOption('multiple'))) && !$keyExists) {
				$element->setValue([]);
				continue;
			}
			//checkboxy na 0 jeśli dane z post i brak wartości
			if ($element instanceof \Mmi\Form\Element\Checkbox && !$keyExists) {
				$element->setValue(0);
				continue;
			}
			//jeśli klucz nie istnieje nie ustawiamy wartości
			if (!$keyExists) {
				continue;
			}
			//ustawianie wartości
			$element->setValue($data[$element->getName()]);
			//ustawianie CTRL
			if ($element->getName() == $this->_formBaseName . '__ctrl') {
				$this->_ctrl = $element->getValue();
			}
		}
		return $this;
	}

	/**
	 * Ustawienie wartości pól
	 * @param \Mmi\Orm\Record $record
	 * @return \Mmi\Form
	 */
	public final function setFromRecord(\Mmi\Orm\Record $record) {
		//dane z rekordu i z opcji
		$data = $record->toArray();
		//sprawdzenie wartości dla wszystkich elementów
		foreach ($this->getElements() as $element) {
			if (!array_key_exists($element->getName(), $data)) {
				continue;
			}
			//ustawianie wartości
			$element->setValue($data[$element->getName()]);
		}
		return $this;
	}

	/**
	 * Czy w modelu wystąpił zapis
	 * @return boolean
	 */
	public final function isSaved() {
		return $this->_saved;
	}

	/**
	 * Zwraca obiekt aktywnego rekordu
	 * @return \Mmi\Orm\Record
	 */
	public final function getRecord() {
		return $this->_record;
	}

	/**
	 * Pobiera nazwę klasy rekordu
	 * @return string
	 */
	public final function getRecordClass() {
		if (!$this->hasRecord()) {
			return;
		}
		//pobranie klasy rekordu
		return get_class($this->_record);
	}

	/**
	 * Czy do formularza przypisany jest active record, jeśli nie, a podana jest nazwa, stworzy obiekt rekordu
	 * @return boolean
	 */
	public final function hasRecord() {
		return $this->_record instanceof \Mmi\Orm\Record;
	}

	/**
	 * Sprawdza czy rekord zawiera dane
	 * @return boolean
	 */
	public final function hasNotEmptyRecord() {
		//jeśli brak rekordu to brak także niepustego rekordu
		if (!$this->hasRecord()) {
			return false;
		}
		//jeśli w rekordzie istnieje choć jedno pole nie będące nullem, zwraca prawdę
		foreach ($this->_record->toArray() as $k => $v) {
			if ($v !== null) {
				return true;
			}
		}
		//wszystkie pola null
		return false;
	}

	/**
	 * Metoda użytkownika wykonywana na koniec konstruktora
	 * odrzuca transakcję jeśli zwróci false
	 */
	public function afterSave() {
		return true;
	}

	/**
	 * Metoda użytkownika wywoływana przed zapisem
	 * odrzuca transakcję jeśli zwróci false
	 * @return boolean
	 */
	public function beforeSave() {
		return true;
	}

	/**
	 * Wywołuje walidację i zapis rekordu powiązanego z formularzem.
	 * @return bool
	 */
	public function save() {
		//jeśli brak rekordu lub formularz nieprawidłowy
		if (!$this->isValid()) {
			return $this->_saved = false;
		}
		//brak rekordu wywoływanie beforeSave() i afterSave()
		if (!$this->hasRecord()) {
			return $this->_saved = (false !== $this->beforeSave()) && (false !== $this->afterSave());
		}
		//wybranie DAO i rozpoczęcie transakcji
		\Mmi\Orm::getAdapter()->beginTransaction();
		//ustawianie danych rekordu
		$this->_setRecordData();
		//metoda przed zapisem, zapis i po zapisie
		//transakcja jest odrzucana w przypadku niepowodzenia którejkolwiek
		if (false === $this->beforeSave() || false === $this->_record->save() || false === $this->afterSave()) {
			//odrzucenie transakcji
			\Mmi\Orm::getAdapter()->rollback();
			return $this->_saved = false;
		}
		//zatwierdzenie transakcji
		\Mmi\Orm::getAdapter()->commit();
		return $this->_saved = true;
	}

	/**
	 * Zapis danych do obiektu rekordu
	 * @return boolean
	 */
	protected final function _setRecordData() {
		$data = [];
		//pobieranie danych z elementów
		foreach ($this->getElements() as $element) {
			//ignorowanie CTRL
			if ($element->getName() == $this->_formBaseName . '__ctrl') {
				continue;
			}
			//dodawanie wartości do tabeli
			$data[$element->getName()] = $element->getValue();
		}
		//ustawianie rekordu na podstawie danych
		$this->_record->setFromArray($data);
	}

	/**
	 * Renderer nagłówka formularza
	 * kalkuluje zmienne kontrolne
	 * @return string
	 */
	public final function start() {
		//nowy hash
		$hash = '';
		//pobranie nazwy klasy
		$class = get_class($this);
		//jeśli zabezpieczony - generowanie hasha
		if ($this->getOption('secured')) {
			\Mmi\Session\Space::factory('\Mmi\Form')->{$this->_formBaseName} = ($hash = md5($class . microtime(true)));
		}
		//ustawianie nowego ctrl
		$this->getElement($this->_formBaseName . '__ctrl')
			->setValue(\Mmi\Convert\Table::toString(['hash' => $hash, 'class' => $class, 'recordClass' => $this->getRecordClass(), 'id' => $this->getRecordClass() ? $this->getRecord()->id : null, 'options' => $this->getOptions()]));
		//zwrot HTML
		return '<form action="' . ($this->getOption('action') ? $this->getOption('action') : '#') .
			'" method="' . $this->getOption('method') .
			'" enctype="' . $this->getOption('enctype') .
			'" class="' . $this->getOption('class') .
			'" accept-charset="' . $this->getOption('accept-charset') .
			'">';
	}

	/**
	 * Renderer stopki formularza
	 * @return string
	 */
	public final function end() {
		return '</form>';
	}

	/**
	 * Automatyczny renderer formularza
	 * @return string
	 */
	public final function render() {
		$html = $this->start();
		//rendering poszczególnych elementów
		foreach ($this->_elements AS $element) {
			/* @var $element \Mmi\Form\Element\ElementAbstract */
			$html .= $element->__toString();
		}
		return $html . $this->end();
	}

	/**
	 * Renderer formularza
	 * Renderuje bezpośrednio, lub z szablonu
	 * @return string
	 */
	public final function __toString() {
		//nie rzuci wyjątkiem, gdyż wyjątki są wyłapane w elementach
		return $this->render();
	}

}
