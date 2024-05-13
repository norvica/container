<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture2b3c6dca
{
    private function __construct()
    {
    }

    public static function create(
        stdClass $a,
        string $b,
    ): self {
        Assert::assertEquals('b', $b, "Failed asserting parameter \$b equals 'b'.");

        return new self();
    }
}
