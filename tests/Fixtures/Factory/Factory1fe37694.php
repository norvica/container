<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Factory;

use PHPUnit\Framework\Assert;
use Tests\Norvica\Container\Fixtures\Result;

final class Factory1fe37694
{
    public function create(
        string ...$a,
    ): Result {
        if (!empty($a)) {
            Assert::assertEquals(['b' => 'b', 'c' => 'c'], $a);
        }

        return new Result();
    }
}
