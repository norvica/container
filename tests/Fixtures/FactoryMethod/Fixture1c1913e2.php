<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture1c1913e2
{
    private function __construct()
    {
    }

    public static function create(
        array $options,
    ): self
    {
        Assert::assertEquals('a', $options['a'], "Failed asserting option 'a' equals 'a'.");
        Assert::assertEquals(3.14, $options['b'], "Failed asserting option 'b' equals 3.14.");
        Assert::assertInstanceOf(stdClass::class, $options['c'], "Failed asserting option 'c' is an instance of \stdClass.");

        return new self();
    }
}
