<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

interface LoggerInterface
{
    public function log(string $message, int $level): void;
}
