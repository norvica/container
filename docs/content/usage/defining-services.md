---
title: "Defining Services"
description: ""
summary: ""
date: 2024-05-29T20:07:40+02:00
lastmod: 2024-05-29T20:07:40+02:00
draft: false
weight: 140
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

Within your configuration file (e.g. `container.php`), you'll define the services that your application depends on.
Services can be of different types, including scalar parameters (simple values), object instances, and collections.
Let's start with scalar parameters.

## Scalar Parameters

Scalar parameters are basic values like strings, numbers, or booleans that your application might need to access
throughout its execution. The Norvica DI Container allows you to define and manage these values easily.

### Using Scalar Values

The simplest way to define a scalar parameter is to assign a value directly:

```php
// container.php

return [
    'app.name' => 'app',
    'app.instances' => 2,
];
```

In this example, the `app.name` parameter will be set to the string `"app"`, and `app.instances` will be set to the
integer `2`.

### Using Anonymous Functions

You can also use anonymous functions (closures) to dynamically calculate the value of a parameter:

```php
// container.php

return [
    'app.name' => static fn() => 'app_' . bin2hex(random_bytes(2)),
];
```

Here, the first time the `app.name` parameter is accessed, a random string starting with "app_" will be generated.

### Using Environment Variables

Often, it's useful to read configuration values from environment variables. Norvica DI Container provides the `env()`
helper function for this:

```php
// container.php
use function Norvica\Container\env;

return [
    'app.name' => env('APP_NAME'), // Get directly from env
    'app.instances' => env('APP_INSTANCES', default: 2)->int(), // Get with a default value and type casting
];
```

* `env('APP_NAME')` fetches the value of the `APP_NAME` environment variable.
* `env('APP_INSTANCES', default: 2)->int()` fetches the value of `APP_INSTANCES`. If not set, it defaults to `2`, and
  the `->int()` part casts it to an integer.

**Important Considerations:**

* **Type Safety:** Be sure to use type casting (`->int()`, `->float()`, `->bool()`) when fetching environment variables
  to ensure your application receives values of the expected data type.

Next, we'll cover defining more complex services in the form of objects in your DI container configuration.

## Services

Services are instances of classes that your application uses. The DI container handles creating these instances,
managing their dependencies, and providing them to your application components as needed.

### Constructor Injection

Constructor injection is the most common and recommended way to define object services. It involves passing the required
dependencies as arguments to the constructor of your class.

#### Without Parameters

If a class doesn't require any constructor arguments, defining it as a service is straightforward:

```php
// container.php
use function Norvica\Container\obj;

return [
    'logger' => obj(Logger::class), 
];
```

Here, `obj(Logger::class)` tells the container to create an instance of the `Logger` class using its constructor.

#### With Parameters

If your class requires dependencies in its constructor, you can provide them like this:

```php
// container.php
use function Norvica\Container\obj;

return [
    'app.name' => 'app',  
    'logger' => obj(Logger::class, name: 'app'), 
];
```

In this example:

* The `app.name` parameter is defined as a string.
* The `logger` service is defined as an instance of the `Logger` class, with the name parameter set to 'app'.

{{< callout context="tip" icon="square-check" >}}
If some parameters are not passed to `obj()`, the container will try to resolve and inject those dependencies
automatically (autowiring).
{{< /callout >}}

### Static Factory Method

Static factory methods are another way to create objects within your DI container. Instead of directly instantiating the
class through its constructor, you call a static method on the class that is responsible for creating and configuring
the object.

#### Without Parameters

If your static factory method doesn't require any parameters, you can define the service like this:

```php
// container.php
use function Norvica\Container\obj;

return [
    'logger' => obj(Logger::create(...)),
];
```

We assume the `Logger` class has a static method called `create()` that returns a new `Logger` instance.

Notice that `...`  is
a [PHP first class callable syntax](https://www.php.net/manual/en/functions.first_class_callable_syntax.php), not an
omitted code.

#### With Parameters

If your static factory method requires parameters, you can pass them in the `obj()` call:

```php
// container.php
use function Norvica\Container\obj;

return [
    'logger' => obj(Logger::create(...), name: 'app', level: 'debug'),
];
```

Here, the `name` and `level` parameters will be passed to the `Logger::create()` method.

### Factory Instance

Factory instances offer a powerful and flexible way to create objects within your DI container. Instead of directly
instantiating the object or using a static factory method, you can use a separate factory object that encapsulates the
creation logic.

#### Without Parameters

```php
// container.php
use function Norvica\Container\obj;
use function Norvica\Container\ref;

return [
    'factory' => obj(LoggerFactory::class),
    'logger' => obj([ref('factory'), 'create']), 
];
```

In this example:

* The `factory` service is defined as an instance of the `LoggerFactory` class.
* The `logger` service is defined using an array syntax: `[ref('factory'), 'create']`. This tells the container to:
  1. Get the `factory` service.
  2. Call the `create` method on that service to create the `logger` instance.

#### With Parameters

You can also pass parameters to the factory method:

```php
// container.php
use function Norvica\Container\obj;
use function Norvica\Container\ref;

return [
    'factory' => obj(LoggerFactory::class),
    'logger' => obj([ref('factory'), 'create'], name: 'app'), 
];
```

Here, the `name` parameter will be passed to the `create` method on the `LoggerFactory` service.

### Anonymous Functions

Anonymous functions (also known as closures) provide a way to define the creation logic for your service directly
within your configuration file.

#### Without Parameters

```php
// container.php

return [
    'logger' => static function(): Logger {
        return new Logger();
    },
];
```

Here, the `logger` service is defined as a closure that simply instantiates a new `Logger` object. The container will
execute this closure to create the `Logger` instance when it's needed.

#### With Parameters

Anonymous functions can also receive parameters, allowing you to inject dependencies or configuration values:

```php
// container.php
use Norvica\Container\Definition\Ref;

return [
    'app.name' => 'app',
    'logger' => static function(
        #[Ref('app.name')] $name, // Inject the 'app.name' parameter
    ): Logger {
        return new Logger(name: $name);
    },
];
```

In this example:

* The `app.name` parameter is defined as a string.
* The anonymous function for the `logger` service receives the `$name` parameter, which is automatically injected by the
* container from the `app.name` definition.
* The function uses the injected `$name` to create the `Logger` instance.

{{< callout context="tip" icon="square-check" >}}
To ensure compatibility with compiled container (when you call `Configurator::snapshot()`), all anonymous functions used
for defining services must be `static` and should not use the `use` keyword to import variables from the surrounding
scope. This guarantees that the compiled container can correctly utilize the closures.
{{< /callout >}}

### Using Setters

Setter injection is another approach to configuring objects within your DI container. You can use setter methods in your
class and use the container to call these methods to inject the dependencies after the object has been created.

```php
// container.php
use function Norvica\Container\obj;
use function Norvica\Container\ref;

return [
    'app.name' => 'app',
    'logger' => obj(Logger::class)
        ->call('setName', ref('app.name')), 
];
```

In this example:

* The `app.name` parameter is defined as a string.
* The logger service is defined using `obj(Logger::class)`, but instead of passing name in the constructor, we chain the
  `call` method.
* `->call('setName', ref('app.name'))` instructs the container to call the `setName` method on the created `Logger`
  instance, passing the value of the `app.name` parameter as the argument.

## Collections

Sometimes, you'll need to inject a group of related services into a component. The Norvica DI Container makes it easy to
define and manage these collections.

Collections are typically represented as arrays in your configuration file. Each element in the array can be a reference
to another service, a new object definition or a value:

```php
// container.php
use function Norvica\Container\obj;
use function Norvica\Container\ref;

return [
    'formatter.json' => obj(JsonFormatter::class), // Service definition
    'formatters' => [
        obj(LineFormatter::class), // New service definition
        ref('formatter.json'), // Reference to another service
    ],
    'logger' => obj(Logger::class, formatters: ref('formatters')), // Inject the collection
];
```

In this example:

* `formatter.json` is a JSON formatter service.
* `formatters` is an array containing two formatters:
  * A new `LineFormatter` instance.
  * A reference to the `formatter.json` service.
* The `logger` service is defined, and the formatters array is injected into its constructor (assuming it has
  a `formatters` argument).

## Aliases

Aliases provide a way to refer to a service by a different name or interface. They act as shortcuts or alternative
names, enhancing the readability and maintainability of your configuration.

## Mapping Interfaces to Implementations

A common use case for aliases is to map interfaces to their concrete implementations:

```php
// container.php
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use Psr\Log\LoggerInterface;

return [
    'logger' => obj(Logger::class),
    LoggerInterface::class => ref('logger'), // Create an alias
];
```

In this example:

* The `logger` service is defined as an instance of the `Logger` class.
* An alias is created for `LoggerInterface::class` that points to the `logger` service.

Now, whenever your code requests an instance of `LoggerInterface`, the container will provide the same `Logger` object 
that was defined under the `logger` ID.
