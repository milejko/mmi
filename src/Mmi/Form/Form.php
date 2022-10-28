<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Form;

use Mmi\App\App;
use Mmi\Db\DbInterface;
use Mmi\Form\Element;
use Mmi\Http\Request;
use Mmi\Mvc\View;

/**
 * Abstrakcyjna klasa komponentu formularza
 * wymaga zdefiniowania metody init()
 * w metodzie init należy skonfigurować pola formularza
 * @method Form setClass($class) ustawia nazwę klasy
 * @method Form setMethod($method) ustawia nazwę metody
 * @method Form setAction($action) ustawia akcję
 */
abstract class Form extends \Mmi\OptionObject
{
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

    //szablon rozpoczynający formularz
    public const TEMPLATE_START = 'mmi/form/start';

    //szablon kończący formularz
    public const TEMPLATE_END = 'mmi/form/end';

    /**
     * Konstruktor
     * @param \Mmi\Orm\Record $record obiekt recordu
     * @param array $options opcje
     */
    public function __construct(\Mmi\Orm\Record $record = null, array $options = [])
    {
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
        $this->isMine() && $this->setFromPost(App::$di->get(Request::class)->getPost());

        //zapis formularza
        $this->save();
    }

    /**
     * Inicjalizacja formularza przez programistę końcowego
     */
    abstract public function init();

    /**
     * Metoda wykonywana przed właściwą walidacją
     * @return boolean
     */
    public function beforeValidation()
    {
        return true;
    }

    /**
     * Metoda walidacji całego formularza (domyślnie zawsze przechodzi)
     * @return boolean
     */
    public function validator()
    {
        return true;
    }

    /**
     * Dodawanie elementu formularza z gotowego obiektu
     * @param \Mmi\Form\Element\ElementAbstract $element obiekt elementu formularza
     * @return self
     */
    public function addElement(\Mmi\Form\Element\ElementAbstract $element)
    {
        //ustawianie opcji na elemencie
        $this->_elements[$element->getBaseName()] = $element->setForm($this);
        return $this;
    }

    /**
     * Usuwanie elementu formularza z gotowego obiektu
     * @param \Mmi\Form\Element\ElementAbstract $element obiekt elementu formularza
     * @return self
     */
    public function removeElement($name)
    {
        if (isset($this->_elements[$name])) {
            unset($this->_elements[$name]);
        }
        return $this;
    }

    /**
     * Zwraca nazwę bazową
     * @return string
     */
    final public function getBaseName()
    {
        return $this->_formBaseName;
    }

    /**
     * Pobranie elementów formularza
     * @return Element\ElementAbstract[]
     */
    final public function getElements()
    {
        return $this->_elements;
    }

    /**
     * Pobranie elementu formularza
     * @param string $name nazwa elementu
     * @return \Mmi\Form\Element\ElementAbstract
     */
    final public function getElement($name)
    {
        return isset($this->_elements[$name]) ? $this->_elements[$name] : null;
    }

    /**
     * Zwraca czy dane POST są przeznaczone dla tego formularza
     * @return boolean
     */
    final public function isMine()
    {
        //sprawdzenie istnienia w POST przestrzeni formularza
        return App::$di->get(Request::class)
            ->getPost()
            ->__isset($this->_formBaseName);
    }

    /**
     * Ustawia czy wysłany formularz jest poprawny
     * @param boolean $valid
     * @return self
     */
    final public function setValid($valid = true)
    {
        $this->_valid = $valid;
        return $this;
    }

    /**
     * Walidacja formularza
     * @return boolean
     */
    final public function isValid()
    {
        //formularz już zwalidowany
        if (null !== $this->_valid) {
            return $this->_valid;
        }
        //dane nie od danego formularza
        if (!$this->isMine()) {
            return $this->_valid = false;
        }
        $validationResult = $this->beforeValidation();
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
    final public function setFromPost(\Mmi\Http\RequestPost $post)
    {
        //dane z posta do tablicy
        $data = $post->toArray()[$this->_formBaseName];
        //sprawdzenie wartości dla wszystkich elementów
        foreach ($this->getElements() as $element) {
            //wyłączone nie są zapisywane z POST
            if ($element->getDisabled()) {
                continue;
            }
            $keyExists = array_key_exists($element->getBaseName(), $data);
            //selecty multiple i serie checkboxów dostają pusty array jeśli:
            //brak wartości oraz dane z POST
            if (($element instanceof Element\MultiCheckbox || ($element instanceof Element\Select && $element->getOption('multiple'))) && !$keyExists) {
                $element->setValue([]);
                continue;
            }
            //checkboxy na false jeśli dane z post i brak wartości
            if ($element instanceof Element\Checkbox) {
                $element->setChecked($keyExists);
                if (!$keyExists) {
                    $element->setValue(false);
                }
                continue;
            }
            //jeśli klucz nie istnieje ustawiamy wartość null
            if (!$keyExists) {
                $element->setValue(null);
                continue;
            }
            //ustawianie wartości
            $element->setValue($data[$element->getBaseName()]);
            //aktualizacja na wartość po filtracji
            $element->setValue($element->getFilteredValue());
        }
        return $this;
    }

    /**
     * Ustawienie wartości pól na podstawie rekordu
     * @param \Mmi\Orm\Record $record
     * @return \Mmi\Form\Form
     */
    public function setFromRecord(\Mmi\Orm\Record $record)
    {
        //dane z rekordu i z opcji
        return $this->setFromArray($record->toArray());
    }

    /**
     * Ustawia wartości pól na podstawie tablicy
     * @param array $data
     * @return \Mmi\Form\Form
     */
    final public function setFromArray(array $data = array())
    {
        //sprawdzenie wartości dla wszystkich elementów
        foreach ($this->getElements() as $element) {
            if (!array_key_exists($element->getBaseName(), $data)) {
                continue;
            }
            //checkbox
            if ($element instanceof Element\Checkbox) {
                $element->getValue() == $data[$element->getBaseName()] ? $element->setChecked() : $element->setChecked(false);
                continue;
            }
            //ustawianie wartości
            $element->setValue($data[$element->getBaseName()]);
        }
        return $this;
    }

    /**
     * Czy w modelu wystąpił zapis
     * @return boolean
     */
    final public function isSaved()
    {
        return $this->_saved;
    }

    /**
     * Zwraca obiekt aktywnego rekordu
     * @return \Mmi\Orm\Record
     */
    final public function getRecord()
    {
        return $this->_record;
    }

    /**
     * Pobiera nazwę klasy rekordu
     * @return string
     */
    final public function getRecordClass()
    {
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
    final public function hasRecord()
    {
        return $this->_record instanceof \Mmi\Orm\Record;
    }

    /**
     * Sprawdza czy rekord zawiera dane
     * @return boolean
     */
    final public function hasNotEmptyRecord()
    {
        //jeśli brak rekordu to brak także niepustego rekordu
        if (!$this->hasRecord()) {
            return false;
        }
        //czy rekord wypełniony
        return $this->getRecord()->getFilled();
    }

    /**
     * Metoda użytkownika wykonywana na koniec konstruktora
     * odrzuca transakcję jeśli zwróci false
     */
    public function afterSave()
    {
        return true;
    }

    /**
     * Metoda użytkownika wywoływana przed zapisem
     * odrzuca transakcję jeśli zwróci false
     * @return boolean
     */
    public function beforeSave()
    {
        return true;
    }

    /**
     * Wywołuje walidację i zapis rekordu powiązanego z formularzem.
     * @return bool
     */
    public function save()
    {
        //jeśli brak rekordu lub formularz nieprawidłowy
        if (!$this->isValid()) {
            return $this->_saved = false;
        }
        //brak rekordu wywoływanie beforeSave() i afterSave()
        if (!$this->hasRecord()) {
            return $this->_saved = (false !== $this->beforeSave()) && (false !== $this->afterSave());
        }
        //iteracja po elementach
        foreach ($this->getElements() as $element) {
            //powiadamianie elementu przed zapisaniem formularza
            $element->beforeFormSave();
        }
        //wybranie DAO i rozpoczęcie transakcji
        App::$di->get(DbInterface::class)->beginTransaction();
        //ustawianie danych rekordu
        $this->_setRecordData();
        //metoda przed zapisem, zapis rekordu
        //transakcja jest odrzucana w przypadku niepowodzenia którejkolwiek
        if (false === $this->beforeSave() || false === $this->_record->save()) {
            //odrzucenie transakcji
            App::$di->get(DbInterface::class)->rollback();
            return $this->_saved = false;
        }
        //powiadomienie elementów o zapisie rekordu - znamy już PK
        foreach ($this->getElements() as $element) {
            //powiadamianie elementu o poprawnym zapisie rekordu
            $element->onRecordSaved();
        }
        //iteracja po elementach
        foreach ($this->getElements() as $element) {
            //powiadamianie elementu o poprawnym zapisie formularza
            $element->onFormSaved();
        }
        //operacje po zapisie rekordu
        if (false === $this->afterSave()) {
            //odrzucenie transakcji
            App::$di->get(DbInterface::class)->rollback();
            return $this->_saved = false;
        }
        //zatwierdzenie transakcji
        App::$di->get(DbInterface::class)->commit();
        return $this->_saved = true;
    }

    /**
     * Zapis danych do obiektu rekordu
     * @return boolean
     */
    final protected function _setRecordData()
    {
        $data = [];
        //pobieranie danych z elementów
        foreach ($this->getElements() as $element) {
            //bez zapisu ignorowanych
            if ($element->getIgnore()) {
                continue;
            }
            //dodawanie wartości do tabeli
            $data[$element->getBaseName()] = $element->getValue();
        }
        //ustawianie rekordu na podstawie danych
        $this->_record->setFromArray($data);
        return true;
    }

    /**
     * Renderer nagłówka formularza
     * kalkuluje zmienne kontrolne
     * @return string
     */
    public function start()
    {
        //form do widoku
        App::$di->get(View::class)->_form = $this;
        //render szablonu
        return App::$di->get(View::class)->renderTemplate(static::TEMPLATE_START);
    }

    /**
     * Renderer stopki formularza
     * @return string
     */
    public function end()
    {
        //form do widoku
        App::$di->get(View::class)->_form = $this;
        //render szablonu
        return App::$di->get(View::class)->renderTemplate(static::TEMPLATE_END);
    }

    /**
     * Automatyczny renderer formularza
     * @return string
     */
    final public function render()
    {
        $html = $this->start();
        //rendering poszczególnych elementów
        foreach ($this->_elements as $element) {
            //ustawienie nazwy po nazwie forma
            $element->setBaseName($this->getBaseName() . '[' . rtrim($element->getBaseName(), '[]') . ']' . (substr($element->getBaseName(), -2) == '[]' ? '[]' : ''));
            /* @var $element \Mmi\Form\Element\ElementAbstract */
            $html .= $element->__toString();
        }
        return $html . $this->end();
    }

    /**
     * Pobranie formularza jako tablicy
     * @return array
     */
    final public function toArray()
    {
        $options = [];

        foreach ($this->getElements() as $element) {
            $options[] = array_merge($element->getOptions(), ['value' => $element->getValue()]);
        }

        return $options;
    }

    /**
     * Pobranie formularza jako jsona
     * @return false|string
     */
    final public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Renderer formularza
     * Renderuje bezpośrednio, lub z szablonu
     * @return string
     */
    final public function __toString()
    {
        //nie rzuci wyjątkiem, gdyż wyjątki są wyłapane w elementach
        return $this->render();
    }
}
