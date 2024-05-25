<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

use DateTimeInterface;
use stdClass;

final class Factory
{
    public function __construct(
        public UnregisteredService $service,
    ) {
    }

    public function create(DateTimeInterface $date): stdClass
    {
        $instance = new stdClass();
        $instance->date = $date->format('Y-m-d');

        return $instance;
    }
}
