<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\CircularDependency;

final class D
{
    public function __construct(E $d)
    {
    }
}
