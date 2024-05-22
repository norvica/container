<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

final readonly class UserRepository
{
    public function __construct(
        public Connection $connection,
    ) {
    }
}
