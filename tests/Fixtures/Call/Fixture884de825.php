<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Call;

use PHPUnit\Framework\Assert;

final class Fixture884de825
{
    public function set(
        string $a,
    ): self {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

        return $this;
    }
}
