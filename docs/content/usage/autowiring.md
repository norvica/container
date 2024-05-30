---
title: "Autowiring"
description: ""
summary: ""
date: 2024-05-29T20:07:40+02:00
lastmod: 2024-05-29T20:07:40+02:00
draft: false
weight: 150
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

Autowiring is a powerful feature that automates the process of resolving and injecting dependencies for your services.
Instead of manually specifying every dependency in your configuration file, autowiring lets the container analyze your
classes and figure out the dependencies based on type hints in constructor arguments or setter methods.

## How It Works

1. **Type Analysis:** When autowiring is enabled, the container inspects the constructor (or setter methods if you are using
   setter injection) of your classes.
2. **Dependency Resolution:** For each constructor parameter or setter method, the container looks for a registered service
   that matches the type hint.
3. **Injection:** If a matching service is found, the container automatically injects it into the object when it's created.

**Example**

```php
// container.php

return  [
    PDO::class => /* definition */,
];
```

```php
// OrderRepository.php
class OrderRepository {
    public function __construct(PDO $pdo) { /* ... */ }
}
```

With autowiring enabled (default behavior), you wouldn't need to explicitly define the `OrderRepository` in your
configuration file. The container would automatically detect that the `OrderRepository` constructor requires a `PDO`
instance and inject it.

**Caveats and Considerations**

* **Ambiguity:** Autowiring can fail if there are multiple services of the same type and the container can't determine
  which one to inject. In such cases, you'll need to provide explicit configuration to resolve the ambiguity.
* **Performance:** While autowiring is generally fast, it might introduce a slight overhead compared to explicit
  configuration. If you're dealing with highly performance-critical code, you might consider compiling container and
  disabling autowiring.

## Disabling Autowiring

By default, autowiring is enabled in the Norvica DI Container. You can disable it using the `Configurator` class:

```php
// container.php
use Norvica\Container\Configurator;

$configurator = new Configurator();
$configurator->autowiring(false); // Disable autowiring
// ... rest of the configuration
```
