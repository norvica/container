<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use DateTime;
use DateTimeZone;
use Psr\Container\ContainerInterface;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\Combined\DateRange;
use Tests\Norvica\Container\Fixtures\Combined\Logger;
use Tests\Norvica\Container\Fixtures\Combined\LoggerInterface;
use Tests\Norvica\Container\Fixtures\Combined\UnregisteredService;
use Tests\Norvica\Container\Fixtures\Combined\UserController;

final class CombinedTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('DB_SUPER_SECRET_PASSWORD=p4$$w0rd');
    }

    public function testContainer(): void
    {
        $container = self::container(__DIR__ . '/../Fixtures/Combined/container.php');
        $this->assertions($container);
    }

    public function testCompiled(): void
    {
        $container = self::compiled(__DIR__ . '/../Fixtures/Combined/container.php');
        $this->assertions($container);
    }

    private function assertions(ContainerInterface $container): void
    {
        $this->assertTrue($container->get('cache.enabled'));
        $this->assertEquals(2, $container->get('db.replicas'));

        $connection = $container->get('db.connection');
        $this->assertEquals('postgresql://user:p4$$w0rd@127.0.0.1:5432/main', $connection->url);

        $service = $container->get('service.user');
        $this->assertInstanceOf(Logger::class, $service->logger);

        $this->assertEquals(
            (new DateTime(timezone: new DateTimeZone('Europe/Berlin')))->format('Y-m-d'),
            $container->get('system.date')->format('Y-m-d'),
        );

        $this->assertEquals('1970-01-01', $container->get('unix')->format('Y-m-d'));

        $logger = $container->get('logger');
        $this->assertEquals('app', $logger->name);
        $this->assertEquals($logger, $container->get(LoggerInterface::class));

        $this->assertInstanceOf(UserController::class, $container->get(UserController::class));

        $unregistered = $container->get(UnregisteredService::class);
        $this->assertInstanceOf(UnregisteredService::class, $unregistered);
        $this->assertEquals($logger, $unregistered->logger);

        $std = $container->get('std.instance');
        $this->assertEquals('1970-01-01', $std->date);

        $range = $container->get(DateRange::class);
        $this->assertEquals('1999-12-31', $range->start->format('Y-m-d'));
        $this->assertEquals('2000-01-01', $range->end->format('Y-m-d'));
    }
}
