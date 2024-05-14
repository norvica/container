<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Constructor;

use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture9d90d88c
{
    public function __construct(
        #[Env('MATH_PI')] $b,
        #[Ref('c')] $c,
    ) {
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");
        Assert::assertInstanceOf(stdClass::class, $c, "Failed asserting parameter \$c is an instance of \stdClass.");
    }
}
