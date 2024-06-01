<?php

declare(strict_types=1);

namespace Tests\Norvica\Container;

use Norvica\Container\Configurator;
use Norvica\Container\InvokerInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class BaseTestCase extends TestCase
{
    protected static array $files = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!is_dir(__DIR__ . '/../var')) {
            mkdir(__DIR__ . '/../var');
        }
    }

    public static function tearDownAfterClass(): void
    {
        parent::tearDownAfterClass();
        foreach (static::$files as $file) {
            if (!is_readable($file)) {
                continue;
            }

            unlink($file);
        }
    }

    protected static function container(array|string $configuration, bool $autowiring = true): ContainerInterface&InvokerInterface
    {
        $configurator = new Configurator();
        $configurator->autowiring($autowiring);
        $configurator->load($configuration);

        return $configurator->container();
    }

    protected static function compiled(array|string $configuration, bool $autowiring = true): ContainerInterface&InvokerInterface
    {
        $configurator = new Configurator();
        $configurator->autowiring($autowiring);
        $configurator->load($configuration);

        $hash = bin2hex(random_bytes(2));
        static::$files[] = __DIR__ . "/../var/Container{$hash}.php";

        return $configurator->snapshot(__DIR__ . "/../var", "Container{$hash}")->container();
    }
}
