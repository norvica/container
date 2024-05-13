<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Constructor;

use PHPUnit\Framework\Assert;

final class Fixture2a2f4b27
{
    public function __construct(
        string ...$a,
    ) {
        if (!empty($a)) {
            Assert::assertEquals(['b' => 'b', 'c' => 'c'], $a);
        }
    }
}
