<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Call;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture578dc31f
{
    public function setA(string $a): self
    {
        Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

        return $this;
    }

    public function setB(float $b): self
    {
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");

        return $this;
    }

    public function setC(stdClass $c): self
    {
        return $this;
    }
}
