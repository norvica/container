<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use PHPUnit\Framework\Assert;

final class Fixture0c18ad61
{
    private function __construct()
    {
    }

    public static function create(
        string $a,
    ): self {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

        return new self();
    }
}
