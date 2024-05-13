<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Factory;

use PHPUnit\Framework\Assert;
use Tests\Norvica\Container\Fixtures\Result;

final class Factory82ea8d38
{
    public function create(
        string $a = 'a',
    ): Result {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

        return new Result();
    }
}
