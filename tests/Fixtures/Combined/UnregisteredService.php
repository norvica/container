<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

final readonly class UnregisteredService
{
    public function __construct(
        public LoggerInterface $logger,
    ) {
    }
}
