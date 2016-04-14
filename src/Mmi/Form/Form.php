<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form;

use Mmi\Form\Element;

/**
 * Abstrakcyjna klasa komponentu formularza
 * wymaga zdefiniowania metody init()
 * w metodzie init należy skonfigurować pola formularza
 * @method Form setClass($class) ustawia nazwę klasy
 * @method Form setMethod($method) ustawia nazwę metody
 * @method Form setAction($action) ustawia akcję
 * 
 * @method Element\Button addElementButton($name) dodaje element button
 * @method Element\Checkbox addElementCheckbox($name) dodaje element checkbox
 * @method Element\File addElementFile($name) dodaje element file
 * @method Element\Hidden addElementHidden($name) dodaje element hidden
 * @method Element\Label addElementLabel($name) dodaje element label
 * @method Element\MultiCheckbox addElementMultiCheckbox($name) dodaje element multicheckbox
 * @method Element\Password addElementPassword($name) dodaje element password
 * @method Element\Radio addElementRadio($name) dodaje element radio
 * @method Element\Select addElementSelect($name) dodaje element select
 * @method Element\Submit addElementSubmit($name) dodaje element submit
 * @method Element\Text addElementText($name) dodaje element text
 * @method Element\Textarea addElementTextarea($name) dodaje element textarea
 * @method Element\Csrf addElementCsrf($name) dodaje element zabezpieczający CSRF
 */
abstract class Form extends \Mmi\OptionObject {

	/**
	 * Elementy formularza
	 * @var Element\ElementAbstract[]
	 */
	protected $_elements = [];

	/**
	 * Nazwa formularza
	 * @var string
	 */
	protected $_formBaseName;

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
		//podłączenie rekordu
		$this->_record = $record;

		//kalkulacja nazwy bazowej formularza
		$this->_formBaseName = strtolower(str_replace('\\', '-', get_class($this)));

		//domyślne opcje
		$this->setClass($this->_formBaseName . ' vertical')
			->setOption('accept-charset', 'utf-8')
			->setMethod('post')
			->setOption('enctype', 'multipart/form-data');

		//opcje przekazywane z konstruktora
		$this->setOptions($options);

		//inicjalizacja formularza
		$this->init();

		//dane z rekordu
		$this->hasNotEmptyRecord() && $this->setFromRecord($this->_record);

		//dane z POST
		$this->isMine() && $this->setFromPost(\Mmi\App\FrontController::getInstance()->getRequest()->getPost());

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
	 * Dodawanie elementu formularza z gotowego obiektu
	 * @param \Mmi\Form\Element\ElementAbstract $element obiekt elementu formularza
	 * @return \Mmi\Form\Element\ElementAbstract
	 */
	public final function addElement(\Mmi\Form\Element\ElementAbstract $element) {
		//ustawianie opcji na elemencie
		return $this->_elements[$element->getName()] = $element->setForm($this);
	}

	/**
	 * Zwraca nazwę bazową
	 * @return string
	 */
	public final function getBaseName() {
		return $this->_formBaseName;
	}

	/**
	 * Pobranie elementów formularza
	 * @return Element\ElementAbstract[]
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
		//sprawdzenie istnienia w POST przestrzeni formularza
		return \Mmi\App\FrontController::getInstance()
				->getRequest()
				->getPost()
				->__isset($this->_formBaseName);
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
		$validationResult = true;
		//walidacja poszczególnych elementów formularza
		foreach ($this->getElements() as $element) {
			//jeśli nieprawidłowy walidacja trwa dalej, ale wynik jest już negatywny
			if (!$element->isValid()) {
				$validationResult = false;
			}
		}
		//rezultat walidacji
		return $this->_valid = $this->validator() && $validationResult;
	}

	/**
	 * Ustawia forma na podstawie obiektu POST
	 * @param \Mmi\Http\RequestPost $post
	 * @return \Mmi\Form
	 */
	public final function setFromPost(\Mmi\Http\RequestPost $post) {
		//dane z posta do tablicy
		$data = $post->toArray()[$this->_formBaseName];
		//sprawdzenie wartości dla wszystkich elementów
		foreach ($this->getElements() as $element) {
			//wyłączone nie są zapisywane z POST
			if ($element->getDisabled()) {
				continue;
			}
			$keyExists = array_key_exists($element->getName(), $data);
			//selecty multiple i serie checkboxów dostają pusty array jeśli:
			//brak wartości oraz dane z POST
			if (($element instanceof Element\MultiCheckbox || ($element instanceof Element\Select && $element->getOption('multiple'))) && !$keyExists) {
				$element->setValue([]);
				continue;
			}
			//checkboxy na 0 jeśli dane z post i brak wartości
			if ($element instanceof Element\Checkbox) {
				$element->setChecked($keyExists);
				continue;
			}
			//jeśli klucz nie istnieje nie ustawiamy wartości
			if (!$keyExists) {
				continue;
			}
			//ustawianie wartości
			$element->setValue($data[$element->getName()]);
			//aktualizacja na wartość po filtracji
			$element->setValue($element->getFilteredValue());
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
			//checkbox
			if ($element instanceof Element\Checkbox) {
				$element->getValue() == $data[$element->getName()] ? $element->setChecked() : $element->setChecked(false);
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
		\Mmi\Orm\DbConnector::getAdapter()->beginTransaction();
		//ustawianie danych rekordu
		$this->_setRecordData();
		//metoda przed zapisem, zapis i po zapisie
		//transakcja jest odrzucana w przypadku niepowodzenia którejkolwiek
		if (false === $this->beforeSave() || false === $this->_record->save() || false === $this->afterSave()) {
			//odrzucenie transakcji
			\Mmi\Orm\DbConnector::getAdapter()->rollback();
			return $this->_saved = false;
		}
		//zatwierdzenie transakcji
		\Mmi\Orm\DbConnector::getAdapter()->commit();
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
			//niezaznaczony checkbox
			if ($element instanceof Element\Checkbox && !$element->issetChecked()) {
				$data[$element->getName()] = 0;
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
		//zwrot HTML
		return '<form action="' . ($this->getOption('action') ? $this->getOption('action') : '#') .
			'" method="' . $this->getOption('method') .
			'" enctype="' . $this->getOption('enctype') .
			'" class="' . $this->getOption('class') .
			'" data-class="' . get_class($this) .
			'" data-record-class="' . $this->getRecordClass() .
			'" data-record-id="' . ($this->hasNotEmptyRecord() ? $this->getRecord()->getPk() : '') .
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

	/**
	 * Magicznie wywoływanie metod
	 * @param string $name
	 * @param array $params
	 * @return mixed
	 */
	public function __call($name, $params) {
		$matches = [];
		//obsługa addElement
		if (preg_match('/addElement([a-zA-Z0-9]+)/', $name, $matches)) {
			$elementClass = '\\Mmi\\Form\\Element\\' . $matches[1];
			return $this->addElement(new $elementClass(isset($params[0]) ? $params[0] : null));
		}
		//obsługa nadrzędnych
		return parent::__call($name, $params);
	}

}
