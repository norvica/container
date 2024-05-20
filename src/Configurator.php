<?php

declare(strict_types=1);

namespace Norvica\Container;

use Closure;
use Norvica\Container\Compiler\ContainerCompiler;
use Norvica\Container\Definition\Definitions;
use Norvica\Container\Definition\Obj;
use Norvica\Container\Definition\Ref;
use Norvica\Container\Definition\Run;
use Norvica\Container\Definition\Val;
use Norvica\Container\Definition\Env;
use Norvica\Container\Exception\ContainerException;
use Psr\Container\ContainerInterface;

final class Configurator
{
    private Definitions $definitions;
    private ContainerInterface|null $container = null;
    private string|null $filepath;
    private bool $compiled;

    public function __construct(
        Definitions|null $definitions = null,
    ) {
        $this->definitions = $definitions ?: new Definitions();
        $this->filepath = null;
        $this->compiled = false;
    }

    public function val(
        string $id,
        string|int|float|bool|null $value,
    ): self {
        if ($this->compiled) {
            throw new ContainerException("Can't add definition '{$id}' as container is already compiled.");
        }

        $this->definitions->add($id, new Val($value));

        return $this;
    }

    public function obj(
        string $id,
        callable|array|string $instantiator,
        mixed ...$arguments,
    ): Obj {
        if ($this->compiled) {
            throw new ContainerException("Can't add definition '{$id}' as container is already compiled.");
        }

        $this->definitions->add($id, $obj = new Obj($instantiator, ...$arguments));

        return $obj;
    }

    public function run(
        string $id,
        callable|array|string $instantiator,
        mixed ...$arguments,
    ): self {
        if ($this->compiled) {
            throw new ContainerException("Can't add definition '{$id}' as container is already compiled.");
        }

        $this->definitions->add($id, new Run($instantiator, ...$arguments));

        return $this;
    }

    public function ref(string $id, string $ref): self
    {
        if ($this->compiled) {
            throw new ContainerException("Can't add definition '{$id}' as container is already compiled.");
        }

        $this->definitions->add($id, new Ref($ref));

        return $this;
    }

    public function load(Definitions|callable|array|string $configuration): self
    {
        if ($this->compiled) {
            throw new ContainerException("Can't load configuration as container is already compiled.");
        }

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

    public function compile(string $filepath, bool $force = false): self
    {
        if ($this->compiled && !$force) {
            throw new ContainerException("Can't compile container to '{$filepath}' as it's already compiled to '{$this->filepath}'.");
        }

        if (is_readable($filepath)) {
            if ($force) {
                return $this->write($filepath);
            }

            return $this->read($filepath);
        }

        return $this->write($filepath);
    }

    public function container(): ContainerInterface
    {
        if ($this->container) {
            return $this->container;
        }

        return $this->container = new Container($this->definitions);
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
                $definition = new Run(instantiator: $definition);
            }

            // implicit scalar value definition
            if (is_scalar($definition) || $definition === null) {
                $definition = new Val($definition);
            }

            // explicit definition
            if (($definition instanceof Obj)
                || ($definition instanceof Run)
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

    private function write(string $filepath): self
    {
        $compiler = new ContainerCompiler($this->definitions);
        file_put_contents($filepath, $compiler->compile());

        return $this->read($filepath);
    }

    private function read(string $filepath): self
    {
        $this->filepath = $filepath;
        $this->compiled = true;
        $this->container = require $filepath;

        return $this;
    }
}
