<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

final readonly class Connection
{
    public function __construct(
        public string $url,
    ) {
    }
}
