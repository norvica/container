<?php

declare(strict_types=1);

namespace Norvica\Container;

use Closure;
use Norvica\Container\Definition\Definitions;
use Norvica\Container\Definition\Obj;
use Norvica\Container\Definition\Ref;
use Norvica\Container\Definition\Val;
use Norvica\Container\Definition\Env;
use Norvica\Container\Exception\ContainerException;

final class Configurator
{
    private Definitions $definitions;

    public function __construct(
        Definitions|null $definitions = null,
    ) {
        $this->definitions = $definitions ?: new Definitions();
    }

    public function val(
        string $id,
        string|int|float|bool|null $value,
    ): self {
        $this->definitions->add($id, new Val($value));

        return $this;
    }

    public function obj(
        string $id,
        callable|array|string $instantiator,
        mixed ...$arguments,
    ): Obj {
        $this->definitions->add($id, $obj = new Obj($instantiator, ...$arguments));

        return $obj;
    }

    public function ref(string $id, string $ref): self
    {
        $this->definitions->add($id, new Ref($ref));

        return $this;
    }

    public function load(Definitions|callable|array|string $configuration): self
    {
        if ($configuration instanceof Definitions) {
            $this->definitions->merge($configuration);

            return $this;
        }

        if (is_string($configuration)) {
            if (!is_readable($configuration)) {
                throw new ContainerException("Cannot load container configuration from '{$configuration}'. File doesn't exist or isn't readable.");
            }

            $configuration = require $configuration;
        }

        if (is_callable($configuration)) {
            $configuration($this);

            return $this;
        }

        if (is_array($configuration)) {
            $this->array($configuration);

            return $this;
        }

        throw new ContainerException(
            sprintf(
                "Loading configuration as '%s' is not supported.",
                get_debug_type($configuration),
            )
        );
    }

    public function definitions(): Definitions
    {
        return $this->definitions;
    }

    private function array(array $configuration): void
    {
        foreach ($configuration as $id => $definition) {
            // implicit anonymous function factory definition
            if ($definition instanceof Closure) {
                $definition = new Obj(instantiator: $definition);
            }

            // implicit scalar value definition
            if (is_scalar($definition) || $definition === null) {
                $definition = new Val($definition);
            }

            // explicit definition
            if (($definition instanceof Obj)
                || ($definition instanceof Val)
                || ($definition instanceof Ref)
                || ($definition instanceof Env)
                || is_array($definition)) {
                $this->definitions->add($id, $definition);

                continue;
            }

            throw new ContainerException(
                sprintf(
                    "Expected definition, got '%s'.",
                    get_debug_type($definition),
                )
            );
        }
    }
}
