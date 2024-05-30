---
title: "Getting Started"
description: ""
summary: ""
date: 2024-05-26T14:07:40+02:00
lastmod: 2024-05-26T14:07:40+02:00
draft: false
weight: 110
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

Requires **PHP 8.2+**.

{{< callout context="tip" icon="square-check" >}}
Use the container for configuring service instantiation and dependency injection. It's designed as a service container,
not a factory, meaning it will instantiate a service only once and return the same instance for all subsequent
requests (singleton pattern). If you need to create multiple instances of an object, consider creating a factory service
and registering it within the container.
{{< /callout >}}

## Install

This library is installed using Composer. If you don't have Composer, you can get it from
[getcomposer.org](https://getcomposer.org).

In your project's root directory, run the following command:

```bash
composer require norvica/container
```

## Create a Configuration

Define your services in a PHP file (e.g., `container.php`):

```php
// container.php

use Norvica\Container\Definition\Env;
use function Norvica\Container\obj;

return [
    'logger' => obj(Logger::class),
    'mailer' => obj(Mailer::class, to: 'user@example.com'),
    'api_client' => static fn(#[Env('API_KEY')] $apiKey) => new ApiClient($apiKey),
];
```

## Instantiate the Container

Use the `Configurator` class to load your configuration and build the container instance:

```php
use Norvica\Container\Configurator;

$configurator = new Configurator();
$configurator->load(__DIR__ . '/container.php');

$container = $configurator->container();
```

## Resolve Dependencies

You can now request services from the container using the `get()` method:

```php
$logger = $container->get('logger');
$mailer = $container->get('mailer'); 
```

## Putting It All Together

**Example:**

```php
// index.php
require 'vendor/autoload.php';

use Norvica\Container\Configurator;
use Monolog\Logger;

// ... container.php (as defined above)

$configurator = new Configurator();
$configurator->load(__DIR__ . '/container.php');
$container = $configurator->container();

$logger = $container->get('logger');
$logger->info('This is a log message.');
```

In this example:

* We require Composer's autoloader.
* We define a `Logger` service in `container.php`.
* We instantiate the `Configurator`, load the configuration file, and build the container.
* We get the `logger` service from the container and use it to log a message.

**Key Points to Remember:**

* **Service IDs:** Use descriptive names for your service IDs to make your configuration readable.
* **Dependency Resolution:** The container automatically resolves dependencies between your services based on type hints and
  the configuration you provide.
* **Singleton Scope:** Services are created as singletons, meaning the container will return the same instance
  each time you request it.
