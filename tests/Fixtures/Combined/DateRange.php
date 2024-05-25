<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Fixtures\Combined;

use DateTime;

final class DateRange
{
    public readonly DateTime $start;
    public readonly DateTime $end;

    /**
     * @param DateTime[] $dates
     */
    public function __construct(
        array $dates,
    ) {
        $this->start = $dates[0];
        $this->end = $dates[1];
    }
}
