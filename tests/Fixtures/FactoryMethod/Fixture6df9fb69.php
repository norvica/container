<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture6df9fb69
{
    private function __construct()
    {
    }

    public static function create(
        string $a,
        float $b,
        stdClass $c,
    ): self {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");

        return new self();
    }
}
