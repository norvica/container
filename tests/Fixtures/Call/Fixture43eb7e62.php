<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Call;

use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use PHPUnit\Framework\Assert;
use stdClass;

final class Fixture43eb7e62
{
    public function setB(#[Env('MATH_PI', type: 'float')] $b): self
    {
        Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");

        return $this;
    }

    public function setC(#[Ref('c')] $c): self
    {
        Assert::assertInstanceOf(stdClass::class, $c, "Failed asserting parameter \$c is instance of \stdClass.");

        return $this;
    }
}
