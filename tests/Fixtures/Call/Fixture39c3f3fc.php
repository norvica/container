<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Call;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture39c3f3fc
{
    public function set(array $options): self
    {
        Assert::assertEquals('a', $options['a'], "Failed asserting option 'a' equals 'a'.");
        Assert::assertEquals(3.14, $options['b'], "Failed asserting option 'b' equals 3.14.");
        Assert::assertInstanceOf(stdClass::class, $options['c'], "Failed asserting option 'c' is an instance of \stdClass.");

        return $this;
    }
}
