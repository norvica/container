---
title: "Compiled Container"
description: ""
summary: ""
date: 2024-05-29T20:07:40+02:00
lastmod: 2024-05-29T20:07:40+02:00
draft: false
weight: 160
toc: true
seo:
  title: "" # custom title (optional)
  description: "" # custom description (recommended)
  canonical: "" # custom canonical URL (optional)
  noindex: false # false (default) or true
---

One of the advanced features of the Norvica DI Container is the ability to compile your container configuration. This
means transforming the PHP code that defines your services into a highly optimized PHP class, resulting in significantly
faster container performance.

## Creating a Snapshot

To compile your container, use the `Configurator::snapshot()` method:

```php
// container.php
use Norvica\Container\Configurator;

$configurator = new Configurator();
$configurator->snapshot(dir: __DIR__ . '/../var/cache', class: 'MyCompiledContainer');
$configurator->load(__DIR__ . '/container.php');

$container = $configurator->container();
```

**How It Works Under the Hood:**

* **Analysis:** The `snapshot()` method analyzes your `container.php` configuration file, identifying services,
  dependencies, and their relationships.
* **Code Generation:** It generates a PHP class (`MyCompiledContainer` in the example) that includes the logic to create
  and configure all your services in a highly optimized way.
* **Caching:** The generated class is cached in the specified directory (`__DIR__ . '/../var/cache'`). On subsequent
  runs, the container will load the cached class instead of re-analyzing the configuration.

{{< callout context="tip" icon="square-check" >}}
When you make changes to your configuration file, you'll need to clear the cached compiled container (e.g., by deleting
the generated file) to ensure that the new configuration takes effect. Don't use compiled container in development environment.
{{< /callout >}}
