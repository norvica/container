<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

final class UserController
{
    public function __construct(
        public UserService $service,
        public LoggerInterface $logger,
    ) {
    }
}
