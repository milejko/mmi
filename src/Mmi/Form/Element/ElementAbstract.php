<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Form\Element;

/**
 * Abstrakcyjna klasa elementu formularza
 * 
 * Gettery
 * @method string getName() pobiera nazwę
 * @method mixed getValue() pobiera wartość pola
 * @method string getId() pobiera nazwę
 * @method string getPlaceholder() pobiera placeholder
 * 
 * Settery
 * @method self setName($name) ustawia nazwę
 * @method self setValue($value) ustawia wartość
 * @method self setId($id) ustawia identyfikator
 * @method self setPlaceholder($placeholder) ustawia placeholder pola
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
 * @method self addValidatorJson($message = null) walidator json
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
abstract class ElementAbstract extends \Mmi\OptionObject
{

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
     * Tablica filtrów
     * @var \Mmi\Filter\FilterAbstract[]
     */
    protected $_filters = [];

    /**
     * Formularz macierzysty
     * @var \Mmi\Form\Form
     */
    protected $_form = null;

    /**
     * Zapisany formularz macierzysty
     * @var boolean
     */
    protected $_formSaved = false;

    /**
     * Kolejność renderowania pola
     * @var array
     */
    protected $_renderingOrder = ['fetchBegin', 'fetchLabel', 'fetchField', 'fetchDescription', 'fetchErrors', 'fetchEnd'];

    //szablon początku pola
    CONST TEMPLATE_BEGIN = 'mmi/form/element/element-abstract/begin';
    //szablon opisu
    CONST TEMPLATE_DESCRIPTION = 'mmi/form/element/element-abstract/description';
    //szablon końca pola
    CONST TEMPLATE_END = 'mmi/form/element/element-abstract/end';
    //szablon błędów
    CONST TEMPLATE_ERRORS = 'mmi/form/element/element-abstract/errors';
    //szablon etykiety
    CONST TEMPLATE_LABEL = 'mmi/form/element/element-abstract/label';
    
    /**
     * Konstruktor
     * @param string $name nazwa
     */
    public function __construct($name)
    {
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
     * @return self
     */
    public final function addClass($className)
    {
        return $this->setOption('class', trim($this->getOption('class') . ' ' . $className));
    }

    /**
     * Dodaje filtr
     * @param \Mmi\Filter\FilterAbstract $filter
     * @return self
     */
    public final function addFilter(\Mmi\Filter\FilterAbstract $filter)
    {
        //dodawanie filtra
        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Dodaje walidator
     * @param \Mmi\Validator\ValidatorAbstract $validator
     * @return self
     */
    public final function addValidator(\Mmi\Validator\ValidatorAbstract $validator)
    {
        //dodawanie walidodatora
        $this->_validators[] = $validator;
        return $this;
    }

    /**
     * Dodaje błąd
     * @param string $error
     * @return ElementAbstract
     */
    public final function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Zdarzenie wywoływane po zapisie
     */
    public function onFormSaved()
    {
        
    }

    /**
     * Ustawia opis
     * @param string $description
     * @return self
     */
    public final function setDescription($description)
    {
        return $this->setOption('data-description', $description);
    }

    /**
     * Ustawia ignorowanie pola
     * @param bool $ignore
     * @return self
     */
    public final function setIgnore($ignore = true)
    {
        return $this->setOption('data-ignore', (bool) $ignore);
    }

    /**
     * Ustawia wyłączenie pola
     * @param bool $disabled
     * @return self
     */
    public final function setDisabled($disabled = true)
    {
        return $disabled ? $this->setOption('disabled', '') : $this;
    }

    /**
     * Ustawia pole do odczytu
     * @param boolean $readOnly
     * @return self
     */
    public final function setReadOnly($readOnly = true)
    {
        return $readOnly ? $this->setOption('readonly', '') : $this;
    }

    /**
     * Ustawia label pola
     * @param string $label
     * @return self
     */
    public final function setLabel($label)
    {
        return $this->setOption('data-label', $label);
    }

    /**
     * Ustawia symbol gwiazdki pól wymaganych
     * @param string $asterisk
     * @return ElementAbstract
     */
    public final function setRequiredAsterisk($asterisk = '*')
    {
        return $this->setOption('data-requiredAsterisk', $asterisk);
    }

    /**
     * Ustawia czy pole jest wymagane
     * @param bool $required wymagane
     * @return self
     */
    public final function setRequired($required = true)
    {
        return $this->setOption('data-required', (bool) $required);
    }

    /**
     * Ustawia postfix labela
     * @param string $labelPostfix postfix labelki
     * @return self
     */
    public final function setLabelPostfix($labelPostfix)
    {
        return $this->setOption('data-labelPostfix', $labelPostfix);
    }

    /**
     * Ustawia form macierzysty
     * @param \Mmi\Form\Form $form
     * @return self
     */
    public function setForm(\Mmi\Form\Form $form)
    {
        $this->_form = $form;
        //ustawianie ID
        $this->setId($form->getBaseName() . '-' . $this->getName());
        return $this;
    }

    /**
     * Ustaw kolejność realizacji
     * @param array $renderingOrder
     * @return ElementAbstract
     */
    public final function setRenderingOrder(array $renderingOrder = [])
    {
        foreach ($renderingOrder as $method) {
            if (!method_exists($this, $method)) {
                throw new \Mmi\Form\FormException('Unknown rendering method');
            }
        }
        $this->_renderingOrder = $renderingOrder;
        return $this;
    }

    /**
     * Pobiera opis
     * @return string
     */
    public final function getDescription()
    {
        return $this->getOption('data-description');
    }

    /**
     * Zwraca czy pole jest ignorowane
     * @return boolean
     */
    public final function getIgnore()
    {
        return (bool) $this->getOption('data-ignore');
    }

    /**
     * Zwraca czy pole jest wyłączone
     * @return boolean
     */
    public final function getDisabled()
    {
        return null !== $this->getOption('disabled');
    }

    /**
     * Pobiera label
     * @return string
     */
    public final function getLabel()
    {
        return $this->getOption('data-label');
    }

    /**
     * Pobiera postfix labelki
     * @return string
     */
    public final function getLabelPostfix()
    {
        return $this->getOption('data-labelPostfix');
    }

    /**
     * Zwraca czy pole jest wymagane
     * @return boolean
     */
    public final function getRequired()
    {
        return (bool) $this->getOption('data-required');
    }

    /**
     * Pobiera walidatory
     * @return \Mmi\Validator\ValidatorAbstract[]
     */
    public final function getValidators()
    {
        return is_array($this->_validators) ? $this->_validators : [];
    }

    /**
     * Pobiera walidatory
     * @return \Mmi\Filter\FilterAbstract[]
     */
    public final function getFilters()
    {
        return is_array($this->_filters) ? $this->_filters : [];
    }

    /**
     * Waliduje pole
     * @return boolean
     */
    public final function isValid()
    {
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
            //dodawanie wiadomości z walidatora
            $this->addError($validator->getMessage() ? $validator->getMessage() : $validator->getError());
        }
        //zwrot rezultatu wszystkich walidacji (iloczyn)
        return $result;
    }

    /**
     * Pobiera błędy pola
     * @return array
     */
    public final function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Filtruje daną wartość za pomocą filtrów pola
     * @param mixed $value wartość
     * @return mixed wynik filtracji
     */
    public function getFilteredValue()
    {
        $val = $this->getValue();
        //iteracja po filtrach
        foreach ($this->getFilters() as $filter) {
            //pobranie filtra, ustawienie opcji i filtracja zmiennej
            $val = $filter->filter($val);
        }
        return $val;
    }

    /**
     * Buduje opcje HTML
     * @return string
     */
    protected final function _getHtmlOptions()
    {
        $validators = $this->getValidators();
        //jeśli istnieją validatory dodajemy klasę validate
        if (!empty($validators)) {
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
     * Buduje kontener pola (początek)
     * @return string
     */
    public final function fetchBegin()
    {
        $class = get_class($this);
        //dodawanie klasy z klasą forma
        $this->addClass(strtolower(substr($class, strrpos($class, '\\') + 1)));
        //dodawanie klasy błędu jeśli wystąpiły
        if ($this->getErrors()) {
            $this->addClass('error');
        }
        //element do widoku
        \Mmi\App\FrontController::getInstance()->getView()->_element = $this;
        //render szablonu
        return \Mmi\App\FrontController::getInstance()->getView()->renderTemplate(self::TEMPLATE_BEGIN);
    }

    /**
     * Buduje kontener pola (koniec)
     * @return string
     */
    public final function fetchEnd()
    {
        //element do widoku
        \Mmi\App\FrontController::getInstance()->getView()->_element = $this;
        //render szablonu
        return \Mmi\App\FrontController::getInstance()->getView()->renderTemplate(self::TEMPLATE_END);
    }

    /**
     * Buduje etykietę pola
     * @return string
     */
    public function fetchLabel()
    {
        if (!$this->getLabel()) {
            return;
        }
        //dodawanie klasy wymagalności
        if ($this->getRequired()) {
            $this->addClass('required');
        }
        //element do widoku
        \Mmi\App\FrontController::getInstance()->getView()->_element = $this;
        //render szablonu
        return \Mmi\App\FrontController::getInstance()->getView()->renderTemplate(self::TEMPLATE_LABEL);
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
    public final function fetchDescription()
    {
        //brak opisu
        if (!$this->getDescription()) {
            return;
        }
        //element do widoku
        \Mmi\App\FrontController::getInstance()->getView()->_element = $this;
        //render szablonu
        return \Mmi\App\FrontController::getInstance()->getView()->renderTemplate(self::TEMPLATE_DESCRIPTION);
    }

    /**
     * Buduje błędy pola
     * @return string
     */
    public final function fetchErrors()
    {
        //element do widoku
        \Mmi\App\FrontController::getInstance()->getView()->_element = $this;
        //render szablonu
        return \Mmi\App\FrontController::getInstance()->getView()->renderTemplate(self::TEMPLATE_ERRORS);
    }

    /**
     * Renderer pola
     * @return string
     */
    public function __toString()
    {
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
     * Obsługa dodawania validatorów i filtrów
     * @param string $name
     * @param array $params
     * @return mixed
     */
    public function __call($name, $params)
    {
        $matches = [];
        //obsługa walidatorów
        if (preg_match('/^addValidator([a-zA-Z0-9]+)/', $name, $matches)) {
            $validatorClass = '\\Mmi\\Validator\\' . $matches[1];
            return $this->addValidator(new $validatorClass($params));
        }
        //obsługa filtrów
        if (preg_match('/^addFilter([a-zA-Z0-9]+)/', $name, $matches)) {
            $filterClass = '\\Mmi\\Filter\\' . $matches[1];
            return $this->addFilter(new $filterClass($params));
        }
        return parent::__call($name, $params);
    }

}
