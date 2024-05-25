<?php

declare(strict_types=1);

use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use Tests\Norvica\Container\Fixtures\Combined\Connection;
use Tests\Norvica\Container\Fixtures\Combined\DateRange;
use Tests\Norvica\Container\Fixtures\Combined\Factory;
use Tests\Norvica\Container\Fixtures\Combined\Logger;
use Tests\Norvica\Container\Fixtures\Combined\LoggerInterface;
use Tests\Norvica\Container\Fixtures\Combined\UserController;
use Tests\Norvica\Container\Fixtures\Combined\UserService;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\run;
use function Norvica\Container\val;

return [
    'cache.enabled' => env('CACHE_ENABLED', default: true)->bool(),
    'db.replicas' => env('DB_REPLICAS', default: 2)->int(),
    'db.host' => '127.0.0.1',
    'db.port' => val('5432'),
    'db.user' => env('DB_USER', 'user'),
    'db.password' => env('DB_SUPER_SECRET_PASSWORD'),
    'db.url' => fn(#[Ref('db.host')] $host, #[Ref('db.port')] $port, #[Ref('db.user')] $user, #[Ref('db.password')] $password, #[Env('DB_NAME', default: 'main')] $name) => "postgresql://{$user}:{$password}@{$host}:{$port}/{$name}",
    'db.connection' => obj(Connection::class, ref('db.url')),
    Connection::class => ref('db.connection'),
    'service.user' => obj(UserService::class)->call('setLogger', ref('logger')),
    UserController::class => obj(UserController::class, ref('service.user')),
    'timezone' => env('APP_TIMEZONE', default: 'Europe/Berlin'),
    'system.timezone' => obj(DateTimeZone::class, timezone: ref('timezone')),
    'system.date' => obj(DateTime::class)
        ->call('setTime', hour: 0, minute: 0)
        ->call('setTimeZone', ref('system.timezone')),
    'unix' => obj(DateTime::createFromFormat(...), 'Y-m-d', '1970-01-01')
        ->call('setTime', 0, 0),
    'logger' => obj(Logger::class, name: 'app'),
    LoggerInterface::class => ref('logger'),
    'std.factory' => obj(Factory::class),
    'std.instance' => run([ref('std.factory'), 'create'], ref('unix')),
    'dates' => [
        obj(DateTime::class, '1999-12-31'),
        run(DateTime::createFromFormat(...), 'Y-m-d', '2000-01-01'),
    ],
    DateRange::class => obj(DateRange::class, ref('dates')),
    // TODO: add more
];
