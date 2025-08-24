# Onion

A collection of [Slytherin](https://github.com/rougin/slytherin)-based HTTP middlewares.

## Installation

Install the package using [Composer](https://getcomposer.org/):

``` bash
$ composer require rougin/onion
```

## Basic usage

To use any of the middlewares, they are needed to be added to the HTTP middleware stack in a `Slytherin` application:

``` php
// index.php

use Rougin\Slytherin\Application;
use Rougin\Onion\JsonHeader;

// Creates a new application instance ---
$app = new Application;
// --------------------------------------

// Adds the middleware to the application ---
$app->add(new JsonHeader);
// ------------------------------------------

// Runs the application ---
$app->run();
// ------------------------
```

## Available middlewares

### `Rougin\Onion\BodyParams`

This middleware parses the request body for complex HTTP methods such as `DELETE`, `PATCH`, and `PUT`. It supports both `application/x-www-form-urlencoded` and `multipart/form-data` content types. This is particularly useful because PHP does not automatically parse the request body for these HTTP methods:

``` php
// index.php

use Rougin\Slytherin\Application;
use Rougin\Onion\BodyParams;

$app = new Application;

$app->add(new BodyParams);

// ...

$app->run();
```

### `Rougin\Onion\CorsHeader`

This middleware adds the necessary headers for [Cross-Origin Resource Sharing (CORS)](https://en.wikipedia.org/wiki/Cross-origin_resource_sharing). It allows to specify which origins and HTTP methods are allowed to access in a application\'s resources:

``` php
// index.php

use Rougin\Slytherin\Application;
use Rougin\Onion\CorsHeader;

$app = new Application;

// Can allow specified origins and methods ---
$origins = array('https://example.com');
$origins[] = 'https://api.example.com';

$methods = array('GET', 'POST', 'PUT');

$app->add(new CorsHeader($origins, $methods));
// -------------------------------------------

// ...

$app->run();
```

### `Rougin\Onion\FormParser`

This middleware parses the request body from `php://input`. It can handle both JSON and `form-urlencoded` data. This is useful for APIs that receive data in the request body:

``` php
// index.php

use Rougin\Slytherin\Application;
use Rougin\Onion\FormParser;

$app = new Application;

$app->add(new FormParser);

// ...

$app->run();
```

### `Rougin\Onion\JsonHeader`

This middleware sets the `Content-Type` header of the response to `application/json` if it has not been set already. This is a convenient way to ensure that the application always returns JSON responses:

``` php
// index.php

use Rougin\Slytherin\Application;
use Rougin\Onion\JsonHeader;

$app = new Application;

$app->add(new JsonHeader);

$app->get('/users', function ($request, $response)
{
    $users = array();

    $users[] = array('id' => 1, 'name' => 'John Doe');
    $users[] = array('id' => 2, 'name' => 'Jane Doe');

    return $response->withJson($users);
});

$app->run();
```

### `Rougin\Onion\NullString`

This middleware converts empty strings, `"null"`, and `"undefined"` values in the request data to `null`. This can be useful for cleaning up input data before it is processed by the application:

``` php
// index.php

use Rougin\Slytherin\Application;
use Rougin\Onion\NullString;

$app = new Application;

$app->add(new NullString);

$app->post('/articles', function ($request, $response)
{
    $data = $request->getParsedBody();

    // Will be "null" if the input is an empty ---
    $author = $data['author'];
    // -------------------------------------------

    return $response;
});

$app->run();
```

## Change log

See [CHANGELOG](CHANGELOG.md) for more recent changes.

## Development

Includes tools for code quality, coding style, and unit tests.

### Code quality

Analyze code quality using [phpstan](https://phpstan.org/):

``` bash
$ phpstan
```

### Coding style

Enforce coding style using [php-cs-fixer](https://cs.symfony.com/):

``` bash
$ php-cs-fixer fix --config=phpstyle.php
```

### Unit tests

Execute unit tests using [phpunit](https://phpunit.de/index.html):

``` bash
$ composer test
```
