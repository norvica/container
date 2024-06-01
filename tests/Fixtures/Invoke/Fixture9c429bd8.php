<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Invoke;

final readonly class Fixture9c429bd8
{
    public function __construct(
        private Fixture289a24a0 $a,
    ) {
    }

    public function __invoke(Fixture3b51a559 $b, string $c): array
    {
        return [$this->a, $b, $c];
    }
}
