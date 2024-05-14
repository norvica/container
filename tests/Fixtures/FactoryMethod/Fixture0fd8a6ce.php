<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture0fd8a6ce
{
    private function __construct()
    {
    }

    public static function create(
        #[Env('MATH_PI')] $b,
        #[Ref('c')] $c,
    ): self
    {
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");
        Assert::assertInstanceOf(stdClass::class, $c, "Failed asserting parameter \$c is an instance of \stdClass.");

        return new self();
    }
}
