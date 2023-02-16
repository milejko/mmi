<?php

$requestedFile = __DIR__ . $_SERVER['PHP_SELF'];

function getMimeType(string $fileName): string
{
    $pathParts = pathinfo($fileName);
    switch ($pathParts['extension']) {
        case 'css':
            return 'text/css';
        case 'woff2':
            return 'application/font-woff2';
        case 'js':
            return 'application/javascript';
    }
    return mime_content_type($fileName);
}

if (file_exists($requestedFile) && is_file($requestedFile)) {
    header('Content-Type: ' . getMimeType($requestedFile));
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS');
    header('Access-Control-Allow-Headers: Accept,Authorization,Cache-Control,Content-Type,DNT,If-Modified-Since,Keep-Alive,Origin,User-Agent,X-Mx-ReqToken,X-Requested-With,salt,X-Request-Origin');
    readfile($requestedFile);
    exit;
}

//application file
require 'index.php';
