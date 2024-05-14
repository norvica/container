<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\CircularDependency;

final class C
{
    public function __construct(D $d)
    {
    }
}
