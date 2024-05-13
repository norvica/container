<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Constructor;

use PHPUnit\Framework\Assert;

final class Fixture87ad4655
{
    public function __construct(
        string $a,
    ) {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");
    }
}
