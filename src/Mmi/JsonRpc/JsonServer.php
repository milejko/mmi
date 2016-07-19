<?php

/**
 * Mmi Framework (https://github.com/milejko/mmi.git)
 * 
 * @link       https://github.com/milejko/mmi.git
 * @copyright  Copyright (c) 2010-2016 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\JsonRpc;
use Mmi\App\FrontController;

class JsonServer {

	/**
	 * Obsługa serwera
	 * @param string $className nazwa klasy_newErrorMethodNotFound
	 * @return string odpowiedź JSON
	 */
	public static function handle($className) {

		$response = new \Mmi\JsonRpc\JsonResponse;
		$request = new \Mmi\JsonRpc\JsonRequest;

		//wczytywanie danych
		try {
			$httpMethod = filter_input(INPUT_SERVER, 'REQUEST_METHOD', FILTER_SANITIZE_SPECIAL_CHARS);
			$getRpcVersion = filter_input(INPUT_GET, 'jsonrpc', FILTER_SANITIZE_SPECIAL_CHARS);
			if ($httpMethod == 'GET' && $getRpcVersion) {
				$request->jsonrpc = $getRpcVersion;
				$request->id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_SPECIAL_CHARS);
				$request->method = filter_input(INPUT_GET, 'method', FILTER_SANITIZE_SPECIAL_CHARS);
				$request->params = json_decode(filter_input(INPUT_GET, 'params', FILTER_SANITIZE_URL), true);
			} else {
				$request = $request->setFromArray(json_decode(file_get_contents('php://input'), true));
			}
			if (!is_array($request->params)) {
				$request->params = [];
			}
			FrontController::getInstance()->getProfiler()->event('JsonRpc\JsonServer received ' . $httpMethod . ': ' . $request->toJson());
		} catch (\Exception $e) {
			$response->error = self::_newErrorInvalidRequest([
					'details' => 'Request is not in a JSON format'
			]);
			return $response->toJson();
		}

		//niewłaściwa wersja jsonrpc
		if ($request->jsonrpc != '2.0') {
			$response->error = self::_newErrorInvalidRequest([
					'details' => 'Missing request "jsonrpc", or not 2.0 version.'
			]);
			return $response->toJson();
		}

		//brak id
		if (!$request->id) {
			$response->error = self::_newErrorInvalidRequest([
					'details' => 'Missing request "id".'
			]);
			return $response->toJson();
		}
		$response->id = $request->id;

		//walidacja nazwy metody
		if (!preg_match('/^[a-z0-9\-\_]+$/i', $request->method)) {
			$response->error = self::_newErrorMethodNotFound([
					'details' => 'Method name "' . $request->method . '" is invalid in class " ' . $className . '".'
			]);
			return $response->toJson();
		}

		//filtrowanie nazwy metody
		$method = strtolower($httpMethod) . ucfirst($request->method);
		//wykonanie metody
		try {
			FrontController::getInstance()->getProfiler()->event('JsonRpc\JsonServer call: ' . $className . '::' . $method . '()');
			if ($method == 'getMethodList') {
				$reflection = new \Mmi\JsonRpc\JsonServerReflection($className);
				$response->result = $reflection->getMethodList();
				return $response->toJson();
			}
			$object = new $className;
			$response->result = call_user_func([$object, $method], $request->params);
			return $response->toJson();
			//wykonanie nie powiodło się
		} catch (JsonDataException $e) {
			$response->error = self::_newError($e->getCode(), $e->getMessage());
			return $response->toJson();
		} catch (JsonGeneralException $e) {
			$response->error = self::_newError($e->getCode(), $e->getMessage());
			return $response->toJson();
		} catch (\Exception $e) {
			//obiekt i metoda istnieją, błąd ilości parametrów
			if (isset($object) && is_object($object) && method_exists($object, $method) && strpos($e->getMessage(), 'WARNING: Missing argument') !== false && strpos($e->getMessage(), 'and defined') === false) {
				$response->error = self::_newErrorInvalidParams([
						'details' => 'Invalid method "' . $method . '" parameter count (' . count($request->params) . ') in class "' . $className . '".',
				]);
				return $response->toJson();
			}
			//błąd metody
			if (isset($object) && is_object($object) && method_exists($object, $method)) {
				FrontController::getInstance()->getLogger()->addWarning($e->getMessage());
				$response->error = self::_newErrorInternal([
						'details' => 'Method "' . $method . '" failed in class "' . $className . '".'
				]);
				return json_encode($response);
			}
			//brak metody w serwisie
			if (isset($object)) {
				$response->error = self::_newErrorMethodNotFound([
						'details' => 'Method "' . $method . '" is not implemented in class "' . $className . '".'
				]);
				return $response->toJson();
			}
			//błąd powołania obiektu
			$response->error = self::_newErrorInternal([
					'details' => 'General service error in class "' . $className . '".'
			]);
			return $response->toJson();
		}
	}

	/**
	 * Bład parsowania żądania
	 * @param array $data opcjonalne dane
	 * @return array
	 */
	protected static function _newErrorParse(array $data = []) {
		return self::_newError(-32700, 'Invalid JSON was received by the server. An error occurred on the server while parsing the JSON text.', $data);
	}

	/**
	 * Błedne żądanie
	 * @param array $data opcjonalne dane
	 * @return array
	 */
	protected static function _newErrorInvalidRequest(array $data = []) {
		return self::_newError(-32600, 'The JSON sent is not a valid Request object.', $data);
	}

	/**
	 * Brak metody
	 * @param array $data opcjonalne dane
	 * @return array
	 */
	protected static function _newErrorMethodNotFound(array $data = []) {
		return self::_newError(-32601, 'The method does not exist / is not available.', $data);
	}

	/**
	 * Bład parametrów metody
	 * @param array $data opcjonalne dane
	 * @return array
	 */
	protected static function _newErrorInvalidParams(array $data = []) {
		return self::_newError(-32602, 'Invalid method parameter(s).', $data);
	}

	/**
	 * Błąd wewnętrzny serwera
	 * @param array $data opcjonalne dane
	 * @return array
	 */
	protected static function _newErrorInternal(array $data = []) {
		return self::_newError(-32603, 'Internal JSON-RPC error.', $data);
	}

	/**
	 * Generuje błąd do odpowiedzi
	 * @param integer $code
	 * @param string $message
	 * @param array $data opcjonalne dane
	 * @return array
	 */
	protected static function _newError($code, $message, array $data = []) {
		return [
			'code' => $code,
			'message' => $message,
			'data' => $data,
		];
	}

}
