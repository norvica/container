<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

final readonly class Val
{
    public function __construct(
        public string|int|float|bool|null $value,
    ) {
    }
}
