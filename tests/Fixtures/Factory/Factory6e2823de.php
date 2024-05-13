<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Factory;

use PHPUnit\Framework\Assert;
use stdClass;
use Tests\Norvica\Container\Fixtures\Result;

final class Factory6e2823de
{
    public function create(
        stdClass $a,
        string $b,
    ): Result {
        Assert::assertEquals('b', $b, "Failed asserting parameter \$b equals 'b'.");

        return new Result();
    }
}
