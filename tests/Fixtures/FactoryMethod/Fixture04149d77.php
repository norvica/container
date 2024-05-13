<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use PHPUnit\Framework\Assert;

final class Fixture04149d77
{
    private function __construct()
    {
    }

    public static function create(
        string $a = 'a',
    ): self {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");
        return new self();
    }
}
