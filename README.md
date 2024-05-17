# Container

Simplify your application structure with a minimalist yet powerful PHP dependency injection container.

> [!WARNING]  
> This library is under active development and is not yet at version `1.0`. While some features or implementation details
> may change in future updates, the core functionality adheres to the PSR-11 container interface, ensuring compatibility
> with other PSR-11 compliant components.

The more stars this repo gets, the faster v1 arrives! 😉

## Features

* **Simple Definitions**: Easily define objects, values, environment variables, and more with a clean configuration.
* **Constructor and Factory Injection**: Automatically resolve dependencies through constructor or static factory method
  injection.
* **Method Calls**: Configure post-creation method calls on your objects for additional setup.
* **Type Hinting and Attributes**: Use type hints and attributes for clear dependency definitions.

## Installation

```shell
composer require norvica/container
```

## Usage

```php
// configuration.php

use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\val;

return [
    'a' => 'foo', // scalar value, same as 'a' => val('foo')
    'b' => env('MATH_PI')->float(), // can be also type-casted to int, and bool
    'c' => obj(C::class), // will be instantiated using constructor with no parameters passed
    'd' => obj(D::class, a: ref('a')), // named parameters will be passed to the constructor
    'e' => obj(E::create(...), a: env('MATH_PI', default: 3.14)->float(), b: 'bar'), // named parameters will be passed to the static factory method
    'f' => static function (
        #[Ref('c')] C $c, 
        #[Env('MATH_PI', type: 'float')] float $b,
    ) {
        return new F($c, $b);
    },
    'g' => obj(G::class)->call('setA', ref('a')), // create class G and call `setA`
    'h' => obj([ref('c'), 'createH'], b: ref('b')), // use service 'c' method 'createH' with parameter 'b'
];
```

```php
$configurator = new Configurator();
$configurator->load(__DIR__ . '/<path-to-your-folder>/configuration.php');
$configurator->load(__DIR__ . '/<path-to-your-folder>/configuration.dev.php');  // your additional configuration for specific environment

$container = $configurator->container();

// access your services:
$serviceC = $container->get('c');
$serviceG = $container->get('g');
```
