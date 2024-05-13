<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Constructor;

use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture83d4d17f
{
    public function __construct(array $options)
    {
        Assert::assertEquals('a', $options['a'], "Failed asserting option 'a' equals 'a'.");
        Assert::assertEquals(3.14, $options['b'], "Failed asserting option 'b' equals 3.14.");
        Assert::assertInstanceOf(stdClass::class, $options['c'], "Failed asserting option 'c' is an instance of \stdClass.");
    }
}
