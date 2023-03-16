<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Form\Element;

use DI\DependencyException;
use DI\NotFoundException;
use Exception;
use Mmi\App\App;
use Mmi\App\KernelException;
use Mmi\Filter\FilterAbstract;
use Mmi\Form\Form;
use Mmi\Form\FormException;
use Mmi\Mvc\View;
use Mmi\OptionObject;
use Mmi\Validator\ValidatorAbstract;

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
 */
abstract class ElementAbstract extends OptionObject
{
    /**
     * Nazwa elementu
     * @var string
     */
    protected $_baseName;

    /**
     * Błędy pola
     * @var array
     */
    protected $_errors = [];

    /**
     * Tablica walidatorów
     * @var ValidatorAbstract[]
     */
    protected $_validators = [];

    /**
     * Tablica filtrów
     * @var FilterAbstract[]
     */
    protected $_filters = [];

    /**
     * Formularz macierzysty
     * @var Form
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

    /**
     * @var View
     */
    protected $view;

    //szablon początku pola
    public const TEMPLATE_BEGIN = 'mmi/form/element/element-abstract/begin';
    //szablon opisu
    public const TEMPLATE_DESCRIPTION = 'mmi/form/element/element-abstract/description';
    //szablon końca pola
    public const TEMPLATE_END = 'mmi/form/element/element-abstract/end';
    //szablon błędów
    public const TEMPLATE_ERRORS = 'mmi/form/element/element-abstract/errors';
    //szablon etykiety
    public const TEMPLATE_LABEL = 'mmi/form/element/element-abstract/label';
    //pusty szablon pola
    public const TEMPLATE_FIELD = '';

    /**
     * Konstruktor
     * @param string $name nazwa
     * @throws DependencyException
     * @throws NotFoundException
     */
    public function __construct($name)
    {
        //ustawia nazwę i opcje domyślne
        $this->setName($name)
            ->setBaseName($name)
            ->setRequired(false)
            ->setRequiredAsterisk('*')
            ->setLabelPostfix(':')
            ->setIgnore(false)
            //dodaje klasę HTML (field)
            ->addClass('field');
        //@TODO: some day better injection (container independent)
        $this->view = App::$di->get(View::class);
    }

    /**
     * Ustawia nazwę podstawową
     * @return string
     */
    public function getBaseName()
    {
        return $this->_baseName;
    }

    /**
     * Pobiera nazwę podstawową
     * @param string $baseName
     * @return $this
     */
    public function setBaseName(string $baseName)
    {
        $this->_baseName = $baseName;

        return $this;
    }

    /**
     * Dodaje klasę do elementu
     * @param string $className nazwa klasy
     * @return self
     */
    final public function addClass($className)
    {
        if (!$this->hasClass($className)) {
            $this->setOption('class', trim($this->getOption('class') . ' ' . $className));
        }
        return $this;
    }

    /**
     * Dodaje klasę do elementu
     * @param string $className nazwa klasy
     * @return self
     */
    final public function removeClass($className)
    {
        if ($this->hasClass($className)) {
            $this->setOption('class', trim(str_replace([$className, '  '], ['', ' '], $this->getOption('class'))));
        }
        return $this;
    }

    /**
     * Dodaje klasę do elementu
     * @param string $className nazwa klasy
     * @return bool
     */
    final public function hasClass($className)
    {
        $class = $this->getOption('class');
        return is_string($class) ? str_contains($class, $className) : false;
    }

    /**
     * Dodaje filtr
     * @param FilterAbstract $filter
     * @return self
     */
    final public function addFilter(FilterAbstract $filter)
    {
        //dodawanie filtra
        $this->_filters[] = $filter;
        return $this;
    }

    /**
     * Dodaje walidator
     * @param ValidatorAbstract $validator
     * @return self
     */
    final public function addValidator(ValidatorAbstract $validator)
    {
        //dodawanie walidodatora
        $this->_validators[] = $validator;
        //dodawanie opisu na podstawie danych z walidatora
        if (empty($this->getDescription())) {
            $this->setDescription($validator->getDescription());
        }
        return $this;
    }

    /**
     * Usuwa walidator
     * @param string $validatorClass
     * @return self
     */
    final public function removeValidator(string $validatorClass)
    {
        foreach ($this->getValidators() as $index => $validator) {
            if (get_class($validator) === $validatorClass) {
                unset($this->_validators[$index]);
            }
        }
        return $this;
    }

    /**
     * Dodaje błąd
     * @param string $error
     * @return ElementAbstract
     */
    final public function addError($error)
    {
        $this->_errors[] = $error;
        return $this;
    }

    /**
     * Zdarzenie wywoływane przed zapisem całego formularza
     */
    public function beforeFormSave()
    {
    }

    /**
     * Zdarzenie wywoływane po zapisie całego formularza
     */
    public function onFormSaved()
    {
    }

    /**
     * Zdarzenie wywoływane po zapisie rekordu - znane PK,
     * ale niewykonane jeszcze afterSave
     */
    public function onRecordSaved()
    {
    }

    /**
     * Ustawia opis
     * @param string $description
     * @return self
     */
    final public function setDescription($description)
    {
        return $this->setOption('data-description', $description);
    }

    /**
     * Ustawia ignorowanie pola
     * @param bool $ignore
     * @return self
     */
    final public function setIgnore($ignore = true)
    {
        return $this->setOption('data-ignore', (bool)$ignore);
    }

    /**
     * Ustawia wyłączenie pola
     * @param bool $disabled
     * @return self
     */
    final public function setDisabled($disabled = true)
    {
        return $disabled ? $this->setOption('disabled', '') : $this;
    }

    /**
     * Ustawia pole do odczytu
     * @param boolean $readOnly
     * @return self
     */
    final public function setReadOnly($readOnly = true)
    {
        return $readOnly ? $this->setOption('readonly', '') : $this;
    }

    /**
     * Ustawia label pola
     * @param string $label
     * @return self
     */
    final public function setLabel($label)
    {
        return $this->setOption('data-label', $label);
    }

    /**
     * Ustawia symbol gwiazdki pól wymaganych
     * @param string $asterisk
     * @return ElementAbstract
     */
    final public function setRequiredAsterisk($asterisk = '*')
    {
        return $this->setOption('data-requiredAsterisk', $asterisk);
    }

    /**
     * Ustawia czy pole jest wymagane
     * @param bool $required wymagane
     * @return self
     */
    final public function setRequired($required = true)
    {
        return $this->setOption('data-required', (bool)$required);
    }

    /**
     * Ustawia postfix labela
     * @param string $labelPostfix postfix labelki
     * @return self
     */
    final public function setLabelPostfix($labelPostfix)
    {
        return $this->setOption('data-labelPostfix', $labelPostfix);
    }

    /**
     * Ustawia form macierzysty
     * @param Form $form
     * @return self
     */
    public function setForm(Form $form)
    {
        $this->_form = $form;
        //ustawianie ID
        $this->setId($form->getBaseName() . '-' . $this->getBaseName());
        //ustawienie nazwy po nazwie forma
        $this->setName($this->_form->getBaseName() . '[' . rtrim($this->getBaseName(), '[]') . ']' . (substr($this->getBaseName(), -2) == '[]' ? '[]' : ''));
        return $this;
    }

    /**
     * Ustaw kolejność realizacji
     * @param array $renderingOrder
     * @return ElementAbstract
     * @throws FormException
     */
    final public function setRenderingOrder(array $renderingOrder = [])
    {
        foreach ($renderingOrder as $method) {
            if (!method_exists($this, $method)) {
                throw new FormException('Unknown rendering method');
            }
        }
        $this->_renderingOrder = $renderingOrder;
        return $this;
    }

    /**
     * Pobiera opis
     * @return string
     */
    final public function getDescription()
    {
        return $this->getOption('data-description');
    }

    /**
     * Zwraca czy pole jest ignorowane
     * @return boolean
     */
    final public function getIgnore()
    {
        return (bool)$this->getOption('data-ignore');
    }

    /**
     * Zwraca czy pole jest wyłączone
     * @return boolean
     */
    final public function getDisabled()
    {
        return null !== $this->getOption('disabled');
    }

    /**
     * Pobiera label
     * @return string
     */
    final public function getLabel()
    {
        return $this->getOption('data-label');
    }

    /**
     * Pobiera postfix labelki
     * @return string
     */
    final public function getLabelPostfix()
    {
        return $this->getOption('data-labelPostfix');
    }

    /**
     * Zwraca czy pole jest wymagane
     * @return boolean
     */
    final public function getRequired()
    {
        return (bool)$this->getOption('data-required');
    }

    /**
     * Pobiera walidatory
     * @return ValidatorAbstract[]
     */
    final public function getValidators()
    {
        return is_array($this->_validators) ? $this->_validators : [];
    }

    /**
     * Pobiera walidatory
     * @return FilterAbstract[]
     */
    final public function getFilters()
    {
        return is_array($this->_filters) ? $this->_filters : [];
    }

    /**
     * Waliduje pole
     * @return boolean
     */
    public function isValid()
    {
        $result = true;
        //waliduje poprawnie jeśli niewymagane, ale tylko gdy niepuste
        if (false === $this->getRequired() && (null === $this->getValue() || '' === $this->getValue())) {
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
    final public function getErrors()
    {
        return $this->_errors;
    }

    /**
     * Dodaje liste błędów
     * @param string $error
     * @return ElementAbstract
     */
    final public function setErrors(array $errors)
    {
        $this->_errors = $errors;
        return $this;
    }

    /**
     * Filtruje daną wartość za pomocą filtrów pola
     * @param mixed $value wartość
     * @return mixed wynik filtracji
     * @throws KernelException
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
    final protected function _getHtmlOptions()
    {
        $validators = $this->getValidators();
        //jeśli istnieją validatory dodajemy klasę validate
        if (!empty($validators)) {
            $this->addClass('validate');
        } else {
            $this->removeClass('validate');
        }
        $html = '';
        //iteracja po opcjach do HTML
        foreach ($this->getOptions() as $key => $value) {
            //ignorowanie niemożliwych do wypisania
            if (!is_string($value) && !is_numeric($value)) {
                continue;
            }
            //placeholder value translation
            if ('placeholder' == $key) {
                $value = $this->view->_($value);
            }
            $html .= $key . '="' . str_replace('"', '&quot;', $value) . '" ';
        }
        //zwrot html
        return $html;
    }

    /**
     * Buduje kontener pola (początek)
     * @return string
     * @throws KernelException
     */
    final public function fetchBegin()
    {
        $class = get_class($this);
        //dodawanie klasy z klasą forma
        $this->addClass(strtolower(substr($class, strrpos($class, '\\') + 1)));
        //dodawanie klasy błędu jeśli wystąpiły
        if ($this->getErrors()) {
            $this->addClass('error');
        } else {
            $this->removeClass('error');
        }
        //element do widoku
        $this->view->_element = $this;
        //render szablonu
        return $this->view->renderTemplate(static::TEMPLATE_BEGIN);
    }

    /**
     * Buduje kontener pola (koniec)
     * @return string
     * @throws KernelException
     */
    final public function fetchEnd()
    {
        //element do widoku
        $this->view->_element = $this;
        //render szablonu
        return $this->view->renderTemplate(static::TEMPLATE_END);
    }

    /**
     * Buduje etykietę pola
     * @return string
     * @throws KernelException
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
        $this->view->_element = $this;
        //render szablonu
        return $this->view->renderTemplate(static::TEMPLATE_LABEL);
    }

    /**
     * Buduje pole
     * @return string
     * @throws KernelException
     */
    public function fetchField()
    {
        //opcje do widoku
        $this->view->_htmlOptions = $this->_getHtmlOptions();
        //element do widoku
        $this->view->_element = $this;
        //render szablonu
        return $this->view->renderTemplate(static::TEMPLATE_FIELD);
    }

    /**
     * Buduje opis pola
     * @return string
     * @throws KernelException
     */
    final public function fetchDescription()
    {
        //brak opisu
        if (!$this->getDescription()) {
            return;
        }
        //element do widoku
        $this->view->_element = $this;
        //render szablonu
        return $this->view->renderTemplate(static::TEMPLATE_DESCRIPTION);
    }

    /**
     * Buduje błędy pola
     * @return string
     * @throws KernelException
     */
    final public function fetchErrors()
    {
        //element do widoku
        $this->view->_element = $this;
        //render szablonu
        return $this->view->renderTemplate(static::TEMPLATE_ERRORS);
    }

    /**
     * Renderer pola
     * @return string
     */
    public function __toString()
    {
        try {
            $html = '';
            foreach ($this->_renderingOrder as $method) {
                if (!method_exists($this, $method)) {
                    continue;
                }
                $html .= $this->{$method}();
            }
            return $html;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }
}
