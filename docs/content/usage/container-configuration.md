---
title: "Container Configuration"
description: ""
summary: ""
date: 2024-05-29T20:07:40+02:00
lastmod: 2024-05-29T20:07:40+02:00
draft: false
weight: 130
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

The Norvica DI Container is configured through a PHP file (let's name it `container.php`), and managed using the
`Configurator` class. This configuration process defines the services, parameters, and relationships that the container
will manage.

## Configuration File

The `container.php` file is a standard PHP return array. Each key in the array represents a service ID (a unique
identifier), and the corresponding value defines how the container should create and manage the service.

**Structure and Format:**

```php
// container.php

return [
    'service_id_1' => /* definition 1 */,
    'service_id_2' => /* definition 2 */,
    // ... more definitions
];
```

1. **Service ID:** Choose meaningful IDs that reflect the purpose of the service (e.g., `logger`, `mailer`). Can also be
   a FQCN (fully qualified class name), e.g. `Foo\Bar`.
2. **Definition:** This can be one of the following:
   * A scalar value (string, number, boolean)
   * An anonymous function (for dynamic value/service creation)
   * A call to a helper function like `obj()`, `ref()`, or `env()` to define object services or dependencies.
   * An array of service definitions (for collections)

## Configurator Class

The `Configurator` class provides the interface for loading the configuration file, customizing container behavior, and
building the final container instance.

**Example Usage:**

```php
use Norvica\Container\Configurator;

$configurator = new Configurator();
$configurator->load(__DIR__ . '/container.php');  // Load configuration

// Optional:
$configurator->snapshot(dir: __DIR__ . '/../var/cache', class: 'MyCompiledContainer');  // Compile (if desired)
$configurator->autowiring(false); // Disable autowiring (if desired)

$container = $configurator->container();  // Get the container instance
```

**Explanation:**

1. A new `Configurator` object is created.
2. The `load()` method reads the definitions from the `container.php` file.
3. Optionally, you can use `snapshot()` to compile the configuration or `autowiring()` to disable autowiring behavior.
4. Finally, `container()` builds and returns the container object, ready for use.

In the following sections, we'll dive deeper into the different ways you can define services and parameters within the
`container.php` file.
