<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

final readonly class Ref
{
    public function __construct(
        public string $id,
    ) {
    }
}
