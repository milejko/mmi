<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 *
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2017 Mariusz Miłejko (mariusz@milejko.pl)
 * @license    https://en.wikipedia.org/wiki/BSD_licenses New BSD License
 */

namespace Mmi\Mvc;

use Mmi\App\KernelException;
use Mmi\Cache\Cache;
use Mmi\Cache\SystemCacheInterface;
use Mmi\Security\Acl;
use Mmi\Security\Auth;
use Mmi\Translate\TranslateInterface;
use Psr\Container\ContainerInterface;

/**
 * Klasa widoku
 * @method string url(array $params = [], $reset = false, $https = null)
 * @method string widget($module, $controller = 'index', $action = 'index', array $params = [])
 * @method ViewHelper\Navigation navigation()
 * @method string template(string $code)
 */
class View extends \Mmi\DataObject
{
    /**
     * Przechowuje dane placeholderów
     * @var array
     */
    private $_placeholders = [];

    /**
     * Wyłączony
     * @var boolean
     */
    private $_layoutDisabled = false;

    /**
     *
     */
    private $acl;

    /**
     *
     */
    private $auth;

    /**
     * @var ContainerInteface
     */
    private $container;

    /**
     * Obiekt requestu
     * @var \Mmi\Http\Request
     */
    public $request;

    /**
     * Adres CDN
     * @var string
     */
    public $cdn;

    /**
     * Constructor
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * Magicznie wywołuje metodę na widoku
     * przekierowuje wywołanie na odpowiedni helper lub placeholder
     * @param string $name nazwa metody
     * @param array $params parametry
     * @return mixed
     */
    public function __call($name, array $params = [])
    {
        //znaleziony helper
        if (null !== $helper = $this->getHelper($name)) {
            return call_user_func_array([$helper, $name], $params);
        }
        //rollback do placeholdera
        return $this->getPlaceholder($name);
    }

    /**
     * Ustawia obiekt request
     * @param \Mmi\Http\Request $request
     * @return \Mmi\Mvc\View
     */
    public function setRequest(\Mmi\Http\Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * Ustawia adres CDN
     * @param string $cdn
     * @return \Mmi\Mvc\View
     */
    public function setCdn($cdn)
    {
        $this->cdn = $cdn;
        return $this;
    }

    /**
     * Set ACL object
     */
    public function setAcl(Acl $acl): self
    {
        $this->acl = $acl;
        return $this;
    }

    /**
     * Gets acl
     */
    public function getAcl(): ?Acl
    {
        return $this->acl;
    }

    /**
     * Set Auth object
     */
    public function setAuth(Auth $auth): self
    {
        $this->auth = $auth;
        return $this;
    }

    /**
     * Gets auth
     */
    public function getAuth(): ?Auth
    {
        return $this->auth;
    }

    /**
     * Zwraca obiekt cache
     */
    public function getSystemCache(): Cache
    {
        return $this->container->get(SystemCacheInterface::class);
    }

    /**
     * Gets messenger object
     */
    public function getMessenger(): Messenger
    {
        return $this->container->get(Messenger::class);
    }

    /**
     * Pobiera helper na podstawie nazwy z uwzględnieniem ścieżek do helperów
     * @param string $name nazwa
     * @return \Mmi\Mvc\ViewHelper\HelperAbstract
     */
    public function getHelper($name)
    {
        return $this->container->has('ViewHelper\\' . \ucfirst($name)) ?
            $this->container->get('ViewHelper\\' . \ucfirst($name)) :
            null;
    }

    /**
     * Pobiera filtr na podstawie nazwy z uwzględnieniem ścieżek do filtrów
     * @param string $name nazwa
     * @return \Mmi\Mvc\ViewHelper\HelperAbstract
     */
    public function getFilter($name)
    {
        return $this->container->has('Filter\\' . \ucfirst($name)) ?
            $this->container->get('Filter\\' . \ucfirst($name)) :
            null;
    }

    /**
     * Ustawia placeholder
     * @param string $name nazwa
     * @param string $content zawartość
     * @return \Mmi\Mvc\View
     */
    public function setPlaceholder($name, $content)
    {
        $this->_placeholders[$name] = $content;
        return $this;
    }

    /**
     * Pobiera placeholder
     * @param string $name nazwa
     * @return string
     */
    public function getPlaceholder($name)
    {
        return isset($this->_placeholders[$name]) ? $this->_placeholders[$name] : null;
    }

    /**
     * Pobiera wszystkie zmienne w postaci tablicy
     */
    public function getAllVariables(): array
    {
        //pobranie danych widoku
        $data = array_merge($this->_data, \get_object_vars($this));
        //iteracja po danych
        foreach ($data as $key => $value) {
            //kasowanie danych prywatnych mmi (zaczynają się od _)
            if ($key[0] == '_') {
                //usuwanie klucza
                unset($data[$key]);
            }
        }
        //zwrot danych
        return $data;
    }

    /**
     * Ustawia wyłączenie layoutu
     * @param boolean $disabled wyłączony
     */
    public function setLayoutDisabled($disabled = true): self
    {
        $this->_layoutDisabled = ($disabled === true) ? true : false;
        return $this;
    }

    /**
     * Czy layout wyłączony
     * @return boolean
     */
    public function isLayoutDisabled()
    {
        return $this->_layoutDisabled;
    }

    /**
     * Pobranie szablonu po ścieżce np. module/controller/action
     * @param string $path
     * @return string|null
     */
    public function getTemplateByPath($path)
    {
        //ścieżka nie jest stringiem
        if (!is_string($path)) {
            throw new \Mmi\Mvc\MvcException('View tpl path invalid');
        }
        //pobranie struktury szablonów
        $structure = $this->container->get('app.structure.template');
        //wyszukiwanie ścieżki w strukturze
        foreach (explode('/', $path) as $dir) {
            if (!isset($structure[$dir])) {
                return;
            }
            //obcinanie struktury
            $structure = $structure[$dir];
        }
        //szablon znaleziony
        if (is_string($structure)) {
            return $structure;
        }
        //szablon nadpisany w projekcie (istniała wersja domyślna w vendorach)
        if (is_array($structure) && isset($structure[0]) && is_string($structure[0])) {
            return $structure[0];
        }
    }

    /**
     * Renderuje i zwraca wynik wykonania template
     * @param string $path ścieżka np. news/index/index
     * @param bool $fetch przekaż wynik wywołania w zmiennej
     * @return string
     */
    public function renderTemplate($path): string
    {
        //wyszukiwanie template
        if (null === $template = $this->getTemplateByPath($path)) {
            throw new KernelException('View tpl not found: ' . $path);
        }
        //kompilacja szablonu
        return $this->_compileTemplate(file_get_contents($template), $this->container->get('app.compile.path') . '/' . $this->container->get(TranslateInterface::class)->getLocale() . '-' . str_replace(['/', '\\'], '-', substr($template, strrpos($template, '/src') + 5, -4) . '.php'));
    }

    /**
     * Renderuje i zwraca wynik wykonania layoutu z ustawionym contentem
     * @param string $content
     * @param \Mmi\Http\Request $request
     */
    public function renderLayout($content, \Mmi\Http\Request $request): string
    {
        return $this->isLayoutDisabled() ? $content : $this
                //ustawianie treści w placeholder 'content'
            ->setPlaceholder('content', $content)
                //renderowanie layoutu
            ->renderTemplate($this->_getLayout($request));
    }

    /**
     * Generowanie kodu PHP z kodu szablonu w locie
     * @param string $templateCode kod szablonu
     */
    public function renderDirectly($templateCode): string
    {
        //kompilacja szablonu
        return $this->_compileTemplate($templateCode, $this->container->get('app.compile.path') . '/' . $this->container->get(TranslateInterface::class)->getLocale() . '-' . md5($templateCode) . '.php');
    }

    /**
     * Skrót translatora
     * @param string $key
     * @param array $params
     */
    public function _($key, array $params = []): string
    {
        return $this->container->get(TranslateInterface::class)->translate($key, $params);
    }

    /**
     * Uruchomienie szablonu
     * @param string $templateCode kod szablonu
     * @param string $compilationFile adres kompilanta
     */
    private function _compileTemplate($templateCode, $compilationFile): string
    {
        //start bufora
        ob_start();
        //wymuszona kompilacja
        if (!$this->getSystemCache()->isActive()) {
            file_put_contents($compilationFile, $this->template($templateCode));
        }
        //próba włączenia skompilowanego pliku
        try {
            //włączenie kompilanta do kodu
            include $compilationFile;
        } catch (\Exception $e) {
            //kompilacja i zapis i włączenie kompilanta do kodu
            file_put_contents($compilationFile, $this->template($templateCode));
            include $compilationFile;
        }
        //przechwycenie danych z bufora
        return ob_get_clean();
    }

    /**
     * Pobiera dostępny layout
     * @param \Mmi\Http\Request $request
     * @throws \Mmi\Mvc\MvcException brak layoutów
     */
    private function _getLayout(\Mmi\Http\Request $request): string
    {
        //test layoutu dla modułu i kontrolera
        if ($this->getTemplateByPath($request->getModuleName() . '/' . $request->getControllerName() . '/layout')) {
            //zwrot layoutu moduł:kontroler
            return $request->getModuleName() . '/' . $request->getControllerName() . '/layout';
        }
        //test layoutu dla modułu
        if ($this->getTemplateByPath($request->getModuleName() . '/layout')) {
            //zwrot layoutu moduł
            return $request->getModuleName() . '/layout';
        }
        //zwrot layoutu aplikacyjnego
        return 'app/layout';
    }
}
