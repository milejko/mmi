# Upgrade to MMi 5.0

## PHP version requirement

Minimum supported PHP version is now **PHP 8.4**. PHP 8.3 and below are no longer supported.

## Dependency changes

### php-di/php-di upgraded to 7.x

`php-di/php-di` has been upgraded from `^6.3` to `^7.0`. Review the [PHP-DI 7.0 migration guide](https://php-di.org/doc/migration/7.0.html) for any breaking changes in your DI configuration.

### doctrine/annotations dropped

`doctrine/annotations` is no longer a dependency. If your code relied on it directly, add it explicitly to your own `composer.json`.

### Build toolkit replaced with explicit dev dependencies

`mmi/mmi-build-toolkit` has been removed. Dev tools are now declared directly:

```json
"require-dev": {
    "phpunit/phpunit": "^13.0",
    "phpmd/phpmd": "^2.13",
    "squizlabs/php_codesniffer": "^3.7",
    "enlightn/security-checker": "*",
    "php-http/mock-client": "^1.5",
    "phpstan/phpstan": "^2.1",
    "friendsofphp/php-cs-fixer": "^3.12",
    "phpstan/phpstan-phpunit": "^2.0"
}
```

Update your project's `require-dev` accordingly.

## Router rename: php-serve-router.php => php-cli-router.php

`src/Mmi/App/executables/php-serve-router.php` has been renamed to `php-cli-router.php` and simplified — it no longer handles MIME types manually (PHP's built-in server handles static files natively).

Update any references in your project's `composer.json` scripts or server configuration.

## Nullable parameter signatures

The following interfaces and classes now use explicit nullable types (`?Type`) instead of `Type = null`. If you extend or implement any of these, update your signatures accordingly.

### DbInterface / PdoAbstract

```php
// before
public function tableInfo(string $tableName, string $schema = null): array;
public function tableList(string $schema = null): array;
public function select(string $fields, string $from, string $where = null, string $groupBy = null, string $order = null, int $limit = null, int $offset = null, array $whereBind = []): array;

// after
public function tableInfo(string $tableName, ?string $schema = null): array;
public function tableList(?string $schema = null): array;
public function select(string $fields, string $from, ?string $where = null, ?string $groupBy = null, ?string $order = null, ?int $limit = null, ?int $offset = null, array $whereBind = []): array;
```

### CacheInterface

```php
// before
public function save($data, string $key, int $lifetime = null): bool;

// after
public function save($data, string $key, ?int $lifetime = null): bool;
```

### EventManagerInterface

```php
// before
public function trigger(string $event, mixed $target = null, array $argv = [], object $callback = null);
public function attach(string $event, object $callback = null, int $priority = 1): object;

// after
public function trigger(string $event, mixed $target = null, array $argv = [], ?object $callback = null);
public function attach(string $event, ?object $callback = null, int $priority = 1): object;
```

### Form

```php
// before
public function __construct(\Mmi\Orm\Record $record = null, array $options = [])
public function removeElement($name)

// after
public function __construct(?\Mmi\Orm\Record $record = null, array $options = [])
public function removeElement(string $name)
```

`getRecordClass()` return type is now `?string` (was untyped but previously always `string`).

## ResponseTypes: private property names changed

`ResponseTypes::$_httpCodes` and `ResponseTypes::$_contentTypes` have been renamed to `$httpCodes` and `$contentTypes`. These are private static properties, so this only affects code that accessed them via reflection.
