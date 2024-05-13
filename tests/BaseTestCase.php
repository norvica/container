<?php

declare(strict_types=1);

namespace Tests\Norvica\Container;

use Norvica\Container\Configurator;
use Norvica\Container\Container;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

abstract class BaseTestCase extends TestCase
{
    protected function container(array $configuration): ContainerInterface
    {
        $configurator = new Configurator();
        $configurator->load($configuration);

        return new Container($configurator->definitions());
    }
}
