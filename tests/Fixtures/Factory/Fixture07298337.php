<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Factory;

use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use PHPUnit\Framework\Assert;
use stdClass;
use Tests\Norvica\Container\Fixtures\Result;

final class Fixture07298337
{
    public function create(
        #[Env('MATH_PI', type: 'float')] $b,
        #[Ref('c')] $c,
    ): Result {
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");
        Assert::assertInstanceOf(stdClass::class, $c, "Failed asserting parameter \$c is an instance of \stdClass.");

        return new Result();
    }
}
