<?php

declare(strict_types=1);

namespace Norvica\Container;

use Closure;

interface InvokerInterface
{
    public function __invoke(Closure|callable|array|string $callable, mixed ...$arguments): mixed;
}
