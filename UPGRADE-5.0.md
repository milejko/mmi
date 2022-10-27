# Upgrade to MMi 5.0

## `Mmi\App\App::run()` method is no longer used
It was replaced by two consecutive methods:
- `Mmi\App\App::handleRequest() : Response`
- `Mmi\App\App::sendResponse(Response $response)`

Please make sure that your `web/index.php` is updated as below:
```php
# in place of:
# (new App())->run();

$app = new App($request);
$response = $app->handleRequest();
$app->sendResponse($response);

```

## Request is now passed as an argument in `Mmi\App\App::__construct()`

This is a workaround, because many components use Request from the DI container.
The plan is to eventually move it as method argument in `Mmi\App\App::handleRequest()`

## Routes are applied to the Request at `handleRequest()` call
Not during the DI Container build as it used to be.

## You can now test the fully-booted app

Since `Mmi\App\App::handleRequest()` returns `Mmi\Http\Response` object instead of directly emitting the response,  
you can make tests by:
1. creating an `App` instance with prepared `Mmi\Http\Request` object
2. inspecting (making assertions on) the returned `Mmi\Http\Response` object

### Caveats:
There is currently no concept of different "environments" in Mmi framework

**Please be extra cautious sure to run the tests with safe ENV values to prevent data loss!**