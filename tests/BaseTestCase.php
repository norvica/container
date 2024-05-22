<?php

declare(strict_types=1);

namespace Tests\Norvica\Container;

use Norvica\Container\Configurator;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class BaseTestCase extends TestCase
{
    protected array $files = [];

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        if (!is_dir(__DIR__ . '/../var')) {
            mkdir(__DIR__ . '/../var');
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        foreach ($this->files as $file) {
            if (!is_readable($file)) {
                continue;
            }

            unlink($file);
        }
    }

    protected function container(array|string $configuration): ContainerInterface
    {
        $configurator = new Configurator();
        $configurator->load($configuration);

        return $configurator->container();
    }

    protected function compiled(array|string $configuration): ContainerInterface
    {
        $configurator = new Configurator();
        $configurator->load($configuration);

        $hash = bin2hex(random_bytes(2));
        $this->files[] = __DIR__ . "/../var/Container{$hash}.php";

        return $configurator->snapshot(__DIR__ . "/../var", "Container{$hash}")->container();
    }
}
