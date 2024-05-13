<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

final class Obj
{
    /**
     * @var callable|array|string
     */
    public $instantiator;

    public array $arguments;

    /**
     * @var Call[]
     */
    public array $calls;

    public function __construct(
        callable|array|string $instantiator,
        mixed ...$arguments,
    ) {
        $this->instantiator = $instantiator;
        $this->arguments = $arguments;
        $this->calls = [];
    }

    public function call(string $method, mixed ...$arguments): self
    {
        $this->calls[] = new Call($method, $arguments);

        return $this;
    }
}
