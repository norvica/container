<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Constructor;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture35f858f6
{
    public function __construct(
        public stdClass $a,
        string $b,
    ) {
        Assert::assertEquals('b', $b, "Failed asserting parameter \$b equals 'b'.");
    }
}
