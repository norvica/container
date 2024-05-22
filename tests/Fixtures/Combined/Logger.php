<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

final readonly class Logger implements LoggerInterface
{
    public function __construct(
        public string $name,
    ) {
    }

    public function log(string $message, int $level): void
    {
    }
}
