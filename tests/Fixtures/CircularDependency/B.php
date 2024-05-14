<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\CircularDependency;

final class B
{
    public function __construct(A $a)
    {
    }
}
