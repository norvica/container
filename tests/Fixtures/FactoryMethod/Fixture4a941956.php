<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\FactoryMethod;

use stdClass;

final class Fixture4a941956
{
    private function __construct()
    {
    }

    public static function create(
        stdClass $a,
    ): self {
        return new self();
    }
}
