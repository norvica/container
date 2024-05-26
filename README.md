# Dependency Injection Container

[![Latest Stable Version](https://poser.pugx.org/norvica/container/v/stable.png)](https://packagist.org/packages/norvica/container)
[![Checks](https://github.com/norvica/container/actions/workflows/checks.yml/badge.svg)](https://github.com/norvica/container/actions/workflows/checks.yml)

Simple yet powerful DI container for PHP, with PSR-11 compliance and easy configuration.

Read the [documentation](https://container.norvica.dev).

> [!IMPORTANT]
> This library is under active development and is not yet at version `1.0`. While some features or implementation details
> may change in future updates, the core functionality adheres to the PSR-11 container interface, ensuring compatibility
> with other PSR-11 compliant components.

## Why Choose This Container?

### Simplicity Empowers Maintainability

Complex tools can quickly become a burden. We believe in the power of simplicity to make your code easier to understand,
debug, and adapt over time. This library avoids unnecessary complexity, focusing on features that offer real value
without sacrificing performance.

### Key Features

- **Fast**: Optimized for performance in production environments.
- **Framework Agnostic**: Integrates seamlessly with any project using the PSR-11 container interface.
- **Explicit Over Implicit**: Prioritizes explicit, readable configuration over hidden "magic", making your code easier
  to maintain.
- **Lightweight**: A minimal footprint keeps your project lean and efficient.
- **Compilation Support**: Optimizes performance further with built-in compilation, including anonymous functions.
- **Autowiring**: Simplify your code with automatic dependency resolution, available even after compilation.
- **Zero-Config Option**: Get started quickly with sensible defaults, no configuration required.
- **Circular Dependency Guard**: Automatically detects and helps you resolve circular dependencies.

### What's Not Included (and Why)

This library intentionally omits certain features to prioritize simplicity, maintainability, and performance:

- **No Property Injection**: We encourage using constructor or method injection for greater clarity and testability. If
  dynamic configuration is needed, consider using anonymous functions within the container definition.
- **No Proxies/Lazy Loading**: These mechanisms often mask underlying design issues. Instead, focus on optimizing your
  code's structure and dependencies for better performance.
- **No "Prototype" Scope**: This library acts as a service container, ensuring consistent object instances. If you need a
  factory pattern, create a factory and register it in the container.
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

## Installation

```shell
composer require norvica/container
```

Read the [documentation](https://container.norvica.dev).
