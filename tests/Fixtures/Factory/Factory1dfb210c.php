<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Factory;

use PHPUnit\Framework\Assert;
use stdClass;
use Tests\Norvica\Container\Fixtures\Result;

final class Factory1dfb210c
{
    public function create(
        string $a,
        float $b,
        stdClass $c,
    ): Result {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");

        return new Result();
    }
}
