---
title : "Container"
description: ""
lead: "Pragmatic PHP dependency injection container, with PSR-11 compliance"
date: 2024-05-26T14:07:40+02:00
lastmod: 2024-05-26T14:07:40+02:00
draft: false
seo:
 title: "PHP validation library" # custom title (optional)
 description: "" # custom description (recommended)
 canonical: "" # custom canonical URL (optional)
 noindex: false # false (default) or true
---

## Key Features

- **Fast**: Optimized for performance in production environments.
- **Framework Agnostic**: Integrates seamlessly with any project using the PSR-11 container interface.
- **Explicit Over Implicit**: Prioritizes explicit, readable configuration over hidden "magic", making your code easier
  to maintain.
- **Lightweight**: A minimal footprint keeps your project lean and efficient.
- **Compilation Support**: Optimizes performance further with built-in compilation, including anonymous functions.
- **Autowiring**: Simplify your code with automatic dependency resolution, available even after compilation.
- **Zero-Config Option**: Get started quickly with sensible defaults, no configuration required.
- **Circular Dependency Guard**: Automatically detects and helps you resolve circular dependencies.

## Configure Service Instantiation

Create and configure service objects directly within your configuration

```php
return [
    'db.connection' => obj(DbConnection::class, name: 'main')->call('setLogger', ref('logger')),
];
```

Or using configurator

```php
return static function(Configurator $configurator) {
    $configurator
        ->obj('db.connection', DbConnection::class, name: 'main')
        ->call('setLogger', ref('logger'));
};
```

## Utilize Factories for Instantiation

Easily manage object creation through factory classes

```php
return [
    'some.factory' => obj(SomeFactory::class),
    'some.instance' => obj([ref('some.factory'), 'create'], foo: 'foo'),
];
```

## Use Environment Variables

Seamlessly integrate environment variables into your service definitions

```php
return [
    'some_feature.enabled' => env('SOME_FEATURE_ENABLED', default: false)->bool(),
    'some.service' => obj(SomeService::class, someFeature: ref('some_feature.enabled')),
    'another.service' => obj(AnotherService::class, foo: env('FOO')),
];
```

## Define Aliases

Simplify referencing by creating aliases for your services, or associating implementations with interfaces

```php
return [
    'logger' => obj(SomeLogger::class),
    LoggerInterface::class => ref('logger'),
];
```

## Leverage Closures for Advanced Composition

Use closures to dynamically create values or service instances

```php
return [
    'db.url' => static fn(
        #[Ref('db.driver')] $driver,
        #[Ref('db.host')] $host, 
        #[Ref('db.port')] $port, 
        #[Ref('db.user')] $user, 
        #[Ref('db.password')] $password, 
        #[Env('DB_NAME', default: 'main')] $name
    ) => "{$driver}://{$user}:{$password}@{$host}:{$port}/{$name}",

    'db.connection' => static function(#[Ref('db.url')] string $url) {
        return new DbConnection(url: $url);
    },
];
```
