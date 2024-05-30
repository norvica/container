---
title: "Introduction"
description: ""
summary: ""
date: 2024-05-26T14:07:40+02:00
lastmod: 2024-05-26T14:07:40+02:00
draft: false
weight: 105
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

This section will provide a foundation for understanding dependency injection (DI) and the benefits of using the Norvica
DI Container in your PHP projects.

## What is Dependency Injection?

Dependency Injection (DI) is a software design pattern that promotes loose coupling between components in your
application. Instead of having objects create or fetch their own dependencies (other objects they need to function), DI
allows you to inject those dependencies from an external source.

## Traditional Approach (Without DI)

```php
class OrderController {
    private $db;

    public function __construct() {
        $this->db = new DatabaseConnection('localhost', 'username', 'password'); // Tight coupling
    }

    // ...
}
```

In this example, the `OrderController` class directly creates its own `DatabaseConnection` object. This creates a tight
coupling between the controller and the database connection implementation.

## Dependency Injection Approach

```php
class OrderController {
    private $db;

    public function __construct(DatabaseConnection $db) { // Dependency injected
        $this->db = $db;
    }

    // ...
}

// ... elsewhere in your application
$db = new DatabaseConnection('localhost', 'username', 'password');
$controller = new OrderController($db); // Inject the dependency
```

Now, the `DatabaseConnection` is passed into the `OrderController`'s constructor (injected). This makes the controller more
flexible and testable because you can easily pass in different database connection implementations or mock objects
during testing.

## Advantages of Using a DI Container

A DI Container (also known as an Inversion of Control container or IoC container) takes the Dependency Injection pattern
further by automating the management and injection of dependencies.

Key Advantages:

* **Reduced Boilerplate:** DI Containers eliminate the need to manually create and wire up objects.
* **Increased Flexibility:** You can easily swap implementations of dependencies without modifying the classes that use
  them. This is crucial for configuration, testing, and adapting to changing requirements.
* **Improved Testability:** DI makes it easier to write tests because you can isolate components and mock their
  dependencies.
* **Better Organization:** DI Containers encourage a more organized and structured codebase by centralizing dependency
  management.

## How the Norvica DI Container Delivers These Benefits:

The Norvica DI Container provides a simple and intuitive way to define your services and their dependencies using a PHP
configuration file. It offers features like:

* **Autowiring:** Automatic dependency resolution based on type hints.
* **Compiled Container:** Significantly improved performance by pre-compiling the container configuration.
* **Flexible Configuration:** Support for various ways to define services (constructor injection, factory methods, etc.).

## What's Not Included (and Why)

This library intentionally omits certain features to prioritize simplicity, maintainability, and performance:

- **No Property Injection**: We encourage using constructor or method injection for greater clarity and testability. If
  dynamic configuration is needed, consider using anonymous functions within the container definition.
- **No Proxies/Lazy Loading**: These mechanisms often mask underlying design issues. Instead, focus on optimizing your
  code's structure and dependencies for better performance.
- **No "Prototype" Scope**: This library acts as a service container, ensuring consistent object instances. If you need
  a factory pattern, create a factory and register it in the container.
- **No Wildcard Definitions**: Wildcard definitions introduce implicit behavior that can become difficult to manage as
  your project grows. We prioritize explicit, readable configurations.
- **No Directory Loading**: Similar to wildcard definitions, directory loading can lead to implicit behavior and
  potential conflicts.
- **No `#[Service]` Attributes**: Explicitly defining services in your configuration makes them easier to locate,
  understand, and manage. This approach also improves performance as there's no need to scan directories for service
  attributes.
- **No Expression Language**: Closures provide enough flexibility for most use cases without introducing unnecessary
  complexity.
- **No Tagging**: Tagging can obscure dependencies. Instead, focus on defining explicit relationships between services.

These choices are made in order to keep the library lean, focused, and easy to understand. By avoiding features
that could introduce unnecessary complexity or ambiguity, we aim to empower you to write cleaner, more maintainable
code.
