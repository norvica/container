<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Call;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture89120f9d
{
    public function set(
        stdClass $a,
        string $b,
    ): self {
        Assert::assertEquals('b', $b, "Failed asserting parameter \$b equals 'b'.");

        return $this;
    }
}
