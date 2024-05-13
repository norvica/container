<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Factory;

use PHPUnit\Framework\Assert;
use stdClass;
use Tests\Norvica\Container\Fixtures\Result;

final class Factory8b7c1a7f
{
    public function create(
        array $options,
    ): Result {
        Assert::assertEquals('a', $options['a'], "Failed asserting option 'a' equals 'a'.");
        Assert::assertEquals(3.14, $options['b'], "Failed asserting option 'b' equals 3.14.");
        Assert::assertInstanceOf(stdClass::class, $options['c'], "Failed asserting option 'c' is an instance of \stdClass.");

        return new Result();
    }
}
