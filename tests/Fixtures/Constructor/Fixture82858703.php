<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Constructor;

use PHPUnit\Framework\Assert;

final class Fixture82858703
{
    public function __construct(
        string $a = 'a',
    ) {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");
    }
}
