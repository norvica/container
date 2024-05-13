<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use PHPUnit\Framework\Assert;

final class Fixture5cb29c3f
{
    private function __construct()
    {
    }

    public static function create(
        string ...$a,
    ): self {
        if (!empty($a)) {
            Assert::assertEquals(['b' => 'b', 'c' => 'c'], $a);
        }

        return new self();
    }
}
