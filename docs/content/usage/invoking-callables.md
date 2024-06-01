---
title: "Invoking Callables"
description: ""
summary: ""
date: 2024-05-29T20:07:40+02:00
lastmod: 2024-05-29T20:07:40+02:00
draft: false
weight: 170
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

The Norvica DI Container provides a convenient way to invoke any callable (function, method, or class with `__invoke()`)
while automatically injecting its dependencies. This can be particularly useful for executing commands, running
background tasks, or triggering specific actions in your application.

## Automatic Dependency Injection

When you invoke a callable through using container as a callable itself, it will inspect the callable's parameters and
automatically resolve any dependencies it can find registered in the container.

## Passing Additional Parameters

Besides injecting dependencies, you can also pass additional parameters to the call:

```php
$result = $container(MyCallable::class, extraArg: 'value');
// or
$result = $container->__invoke(MyCallable::class, extraArg: 'value');
```

Let's illustrate how to invoke different types of callables.

## Classes with `__invoke()` Method

```php
class MyCommand {
    public function __construct(LoggerInterface $logger) { /* ... */ }

    public function __invoke(string $message) {
        $this->logger->info($message);
    }
}

// ...
$container(MyCommand::class, message: 'Command is executed.');
// or
$container->__invoke(MyCommand::class, message: 'Command is executed.');
```

## Class Methods

```php
use function Norvica\Container\ref;

class MyService {
    public function processData(string $data) { /* ... */ }
}

// ...
$container([ref(MyService::class), 'processData'], data: 'some_data');
// or
$container->__invoke([ref(MyService::class), 'processData'], data: 'some_data');
```

## Static Methods

```php
class Utils {
    public static function generateReport(PDO $db, string $startDate, string $endDate) { /* ... */ }
}

// ...
$container(Utils::generateReport(...), startDate: '2024-01-01', endDate: '2024-01-31');
// or
$container->__invoke(Utils::generateReport(...), startDate: '2024-01-01', endDate: '2024-01-31');
```

## Anonymous Functions

```php
$container(
    function (LoggerInterface $logger, string $message) {
        $logger->info($message);
    },
    message: 'Logging a message from the closure',
);
// or
$container->__invoke(
    function (LoggerInterface $logger, string $message) {
        $logger->info($message);
    },
    message: 'Logging a message from the closure',
);
```
