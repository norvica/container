<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Call;

use PHPUnit\Framework\Assert;

final class Fixture9127af36
{
    public function set(string ...$a): self
    {
        if (!empty($a)) {
            Assert::assertEquals(['b' => 'b', 'c' => 'c'], $a);
        }

        return $this;
    }
}
