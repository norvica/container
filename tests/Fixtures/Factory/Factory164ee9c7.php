<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Factory;

use PHPUnit\Framework\Assert;
use Tests\Norvica\Container\Fixtures\Result;

final class Factory164ee9c7
{
    public function create(
        string $a,
    ): Result {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

        return new Result();
    }
}
