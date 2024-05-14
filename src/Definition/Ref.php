<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final readonly class Ref
{
    public function __construct(
        public string $id,
    ) {
    }
}
