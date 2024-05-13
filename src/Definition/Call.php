<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

final readonly class Call
{
    public function __construct(
        public string $method,
        public array $arguments,
    ) {
    }
}
