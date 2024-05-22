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
    private string|null $dir;
    private string|null $class;
    private bool $locked;

    public function __construct(
        Definitions|null $definitions = null,
    ) {
        $this->definitions = $definitions ?: new Definitions();
        $this->dir = null;
        $this->locked = false;
        $this->class = 'Container';
    }

    public function val(
        string $id,
        string|int|float|bool|null $value,
    ): self {
        if ($this->locked) {
            throw new ContainerException("Can't add definition '{$id}' as container is locked.");
        }

        $this->definitions->add($id, new Val($value));

        return $this;
    }

    public function obj(
        string $id,
        callable|array|string $instantiator,
        mixed ...$arguments,
    ): Obj {
        if ($this->locked) {
            throw new ContainerException("Can't add definition '{$id}' as container is locked.");
        }

        $this->definitions->add($id, $obj = new Obj($instantiator, ...$arguments));

        return $obj;
    }

    public function run(
        string $id,
        callable|array|string $instantiator,
        mixed ...$arguments,
    ): self {
        if ($this->locked) {
            throw new ContainerException("Can't add definition '{$id}' as container is locked.");
        }

        $this->definitions->add($id, new Run($instantiator, ...$arguments));

        return $this;
    }

    public function ref(string $id, string $ref): self
    {
        if ($this->locked) {
            throw new ContainerException("Can't add definition '{$id}' as container is locked.");
        }

        $this->definitions->add($id, new Ref($ref));

        return $this;
    }

    public function load(Definitions|callable|array|string $configuration): self
    {
        if ($this->locked) {
            throw new ContainerException("Can't load definitions when container is locked.");
        }

        if (class_exists($this->class)) {
            return $this;
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

    public function snapshot(string $dir, string $class = 'Container'): self
    {
        if ($this->locked) {
            throw new ContainerException("Can't compile container to '{$dir}' as it's locked.");
        }

        $this->dir = $dir;
        $this->class = $class;

        return $this;
    }

    public function container(): ContainerInterface
    {
        if ($this->container) {
            return $this->container;
        }

        if ($this->dir) {
            $this->compile();
            $this->locked = true;

            return $this->container;
        }

        $this->locked = true;

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

    private function compile(): void
    {
        if (!class_exists($this->class)) {
            $filename = $this->dir . DIRECTORY_SEPARATOR . $this->class . '.php';
            if (!file_exists($filename)) {
                $compiler = new ContainerCompiler($this->definitions);
                file_put_contents($filename, $compiler->compile($this->class)); // TODO: use atomic file write
            }

            require $filename;
        }

        $this->container = new Container(new Definitions(), new ($this->class)());
    }
}
