<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\JsonRpc;

use ReflectionClass,
    ReflectionMethod,
    ReflectionProperty;

class JsonServerReflection
{

    /**
     * Refleksja klasy głównej
     * @var ReflectionClass
     */
    protected $_reflectionClass;

    /**
     * Tworzenie reflektora klasy
     * @param string $className
     */
    public function __construct($className)
    {
        new $className;
        $this->_reflectionClass = new ReflectionClass($className);
    }

    /**
     * Pobiera listę metod
     * @return array
     */
    public function getMethodList()
    {
        $methods = [];
        //iteracja po metodach klasy
        foreach ($this->_reflectionClass->getMethods(ReflectionMethod::IS_PUBLIC) as $methodRow) {
            //jeśli metoda nie ma przedrostka get put post delete - jest pomijana
            if (!preg_match('/^(get|put|post|delete)/', substr($methodRow->name, 0, 6), $httpMethod)) {
                continue;
            }
            //wtorzenie reflektora metody
            $methodReflection = new ReflectionMethod($methodRow->class, $methodRow->name);
            $comment = $methodReflection->getDocComment();
            $params = [];
            //iteracja po parametrach metody
            foreach ($methodReflection->getParameters() as $param) {
                if (!preg_match('/\@param\s([a-zA-Z]+)\s\$' . $param->name . '/', $comment, $type)) {
                    $params[$param->name] = 'string';
                    continue;
                }
                $params[$param->name]['type'] = $type[1];
                //opisy parametrów
                if (preg_match('/\@param\s([a-zA-Z]+)\s\$' . $param->name . '\ (.[^\n]+)/', $comment, $comm)) {
                    if (class_exists($comm[2])) {
                        $dtoClassName = $comm[2];
                        $dtoReflection = new ReflectionClass($dtoClassName);
                        $props = [];
                        foreach ($dtoReflection->getProperties(ReflectionProperty::IS_PUBLIC) as $prop) {
                            $props[] = $prop->name . ' => ?';
                        }
                        $params[$param->name]['type'] = 'array(' . implode(', ', $props) . ')';
                    } else {
                        $params[$param->name]['comment'] = trim($comm[2]);
                    }
                }
            }
            //typ prosty
            if (preg_match('/\@return\s([a-zA-Z\|_]+)/', $comment, $return)) {
                $return = $return[1];
                if (strpos($return, '\\') !== false && class_exists($return)) {
                    $return = $this->_classFieldsArrayString($return);
                }
            } else {
                //pozostałe typy traktowane jak string
                $return = 'string';
            }
            $paramStr = '';
            //iteracja po parametrach
            foreach ($params as $param => $data) {
                if (!isset($data['type'])) {
                    $paramStr .= ' $' . $param . ', ';
                    continue;
                }
                //typ tablicowy
                if (substr($data['type'], 0, 5) == 'array') {
                    $data['type'] = 'array';
                }
                $paramStr .= $data['type'] . ' $' . $param . ', ';
            }
            $commentArr = explode("\n", $comment);
            //dodawanie metody
            $methods[] = [
                'definition' => $methodRow->name . '(' . trim($paramStr, ', ') . ');',
                'comment' => isset($commentArr[1]) ? trim($commentArr[1], '*	 ') : '',
                'HTTP method' => strtoupper($httpMethod[1]),
                'RPC method' => lcfirst(substr($methodRow->name, strlen($httpMethod[1]))),
                'parameter count' => count($params),
                'parameter details' => $params,
                'return' => $return,
            ];
        }
        //zwrot metod
        return $methods;
    }

    /**
     * Zwraca pola klasy
     * @param nazwa klasy $className
     * @return string
     */
    protected function _classFieldsArrayString($className)
    {
        $class = new $className;
        $classStr = '[';
        //iterator klasy
        foreach ($class as $field => $value) {
            $classStr .= $field . ' => ?, ';
        }
        return rtrim($classStr, ', ') . ']';
    }

}
