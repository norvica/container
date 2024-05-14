<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\CircularDependency;

final class A
{
    public function __construct(B $a)
    {
    }
}
