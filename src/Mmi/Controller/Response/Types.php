<?php

/**
 * Mmi Framework (https://bitbucket.org/mariuszmilejko/mmicms/)
 * 
 * @link       https://bitbucket.org/mariuszmilejko/mmicms/
 * @copyright  Copyright (c) 2010-2015 Mariusz Miłejko (http://milejko.com)
 * @license    http://milejko.com/new-bsd.txt New BSD License
 */

namespace Mmi\Controller\Response;

class Types {

	/**
	 * Przechowuje kody HTTP
	 * @var array
	 */
	private static $_httpCodes = [
		100 => 'Continue',
		101 => 'Switching Protocols',
		110 => 'Connection Timed Out',
		111 => 'Connection refused',
		200 => 'OK',
		201 => 'Created',
		202 => 'Accepted',
		203 => 'Non-Authoritative Information',
		204 => 'No content',
		205 => 'Reset Content',
		206 => 'Partial Content',
		300 => 'Multiple Choices',
		301 => 'Moved Permanently',
		302 => 'Found',
		303 => 'See Other',
		304 => 'Not Modifie',
		305 => 'Use Proxy',
		306 => 'Switch Proxy',
		307 => 'Temporary Redirect',
		310 => 'Too many redirects',
		400 => 'Bad Request',
		401 => 'Unauthorized',
		402 => 'Payment Required',
		403 => 'Forbidden',
		404 => 'Not Found',
		405 => 'Method Not Allowed',
		406 => 'Not Acceptable',
		407 => 'Proxy Authentication Required',
		408 => 'Request Timeout',
		409 => 'Conflict',
		410 => 'Gone',
		411 => 'Length required',
		412 => 'Precondition Failed',
		413 => 'Request Entity Too Large',
		414 => 'Request-URI Too Long',
		415 => 'Unsupported Media Type',
		416 => 'Requested Range Not Satisfiable',
		417 => 'Expectation Failed',
		500 => 'Internal Server Error',
		501 => 'Not Implemented',
		502 => 'Bad Gateway',
		503 => 'Service Unavailable',
		504 => 'Gateway Timeout',
		505 => 'HTTP Version Not Supported',
	];

	/**
	 * Przechowuje content-type
	 * @var array
	 */
	private static $_contentTypes = [
		'htm' => 'text/html',
		'html' => 'text/html',
		'shtml' => 'text/html',
		'txt' => 'text/plain',
		'css' => 'text/css',
		'xml' => 'text/xml',
		'mml' => 'text/mathml',
		'htc' => 'text/x-component',
		'gif' => 'image/gif',
		'png' => 'image/png',
		'jpg' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'tif' => 'image/tiff',
		'tiff' => 'image/tif',
		'ico' => 'image/x-icon',
		'jng' => 'image/x-jng',
		'bmp' => 'image/x-ms-bmp',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',
		'js' => 'application/x-javascript',
		'atom' => 'application/atom+xml',
		'json' => 'application/json',
		'ps' => 'application/postscript',
		'rtf' => 'text/rtf',
		'doc' => 'application/msword',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',
		'xhtml' => 'application/xhtml+xml',
		'zip' => 'application/zip',
		'gz' => 'application/gzip',
		'odt' => 'application/octet-stream',
		'bin' => 'application/octet-stream',
		'midi' => 'audio/midi',
		'mp3' => 'audio/mpeg',
		'oga' => 'audio/ogg',
		'mp4' => 'video/mp4',
		'mpg' => 'video/mpeg',
		'ogv' => 'video/ogg',
		'mkv' => 'video/x-matroska',
		'avi' => 'video/x-msvideo',
		'flv' => 'video/x-flv',
		'wmv' => 'video/x-ms-wmv',
		'wmv' => 'video/x-ms-asf',
		'mov' => 'video/quicktime',
		'exe' => 'application/x-dosexec',
		'msi' => 'application/x-msi',
		'gz' => 'application/x-gzip',
		'pdf' => 'application/pdf',
		'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
		'xlsm' => 'application/vnd.ms-excel.sheet.macroEnabled.12',
		'ppt' => 'application/vnd.ms-powerpoint',
		'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
	];
	
	/**
	 * Pobiera komunikat HTTP po kodzie
	 * @param integer $code
	 * @return string
	 */
	public static function getMessageByCode($code) {
		return isset(self::$_httpCodes[$code]) ? self::$_httpCodes[$code] : null;
	}

	/**
	 * Pobiera mime/type po rozszerzeniu
	 * @param string $extension
	 * @return string
	 */
	public static function getTypeByExtension($extension) {
		return isset(self::$_contentTypes[$extension]) ? self::$_contentTypes[$extension] : null;
	}
	
	/**
	 * Pobiera rozszerzenie po mime/type
	 * @param string $type
	 * @return string
	 */
	public static function getExtensionByType($type) {
		return array_keys(self::$_contentTypes, $type);
	}

}
