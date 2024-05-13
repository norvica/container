<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Constructor;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture01dc015b
{
    public function __construct(
        string $a,
        float $b,
        public stdClass $c,
    ) {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");
    }
}
