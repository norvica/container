<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\CircularDependency;

final class E
{
    public function __construct(C $c)
    {
    }
}
