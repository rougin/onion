# Onion

A collection of [Slytherin](https://github.com/rougin/slytherin)-based HTTP middlewares.

## Installation

Install the package using [Composer](https://getcomposer.org/):

``` bash
$ composer require rougin/onion
```

## Middlewares

`Rougin\Onion\FormParser`
- Parses input request coming from `php:://input`.

`Rougin\Onion\BodyParams`
- Parses input request from complex HTTP methods (`DELETE`, `PATCH`, `PUT`).

`Rougin\Onion\NullString`
- Converts `null` string as a `null` literal.

`Rougin\Onion\CorsHeader`
- Adds additional headers for [Cross-origin resource sharing (CORS)](https://en.wikipedia.org/wiki/Cross-origin_resource_sharing).

`Rougin\Onion\JsonHeader`
- Changes the content response to `application/json`.

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
