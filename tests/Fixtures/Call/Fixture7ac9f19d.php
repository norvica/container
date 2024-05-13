<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Call;

use PHPUnit\Framework\Assert;

final class Fixture7ac9f19d
{
    public function set(
        string $a = 'a',
    ): self {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

        return $this;
    }
}
