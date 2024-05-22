<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

final class UserService
{
    public LoggerInterface|null $logger = null;

    public function __construct(
        public readonly UserRepository $repository,
    ) {
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }
}
