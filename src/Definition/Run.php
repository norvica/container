<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

final class Run
{
    /**
     * @var callable|array
     */
    public $instantiator;

    public array $arguments;

    public function __construct(
        callable|array $instantiator,
        mixed ...$arguments,
    ) {
        $this->instantiator = $instantiator;
        $this->arguments = $arguments;
    }
}
