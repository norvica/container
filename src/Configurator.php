<?php

declare(strict_types=1);

namespace Norvica\Container;

use Closure;
use Norvica\Container\Compiler\Compiler;
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
    private const INITIALIZING = 0;
    private const COMPILING = 1;
    private const LOADING = 2;
    private const INITIALIZED  = 4;

    private Definitions $definitions;
    private ContainerInterface|null $container = null;
    private string|null $class = null;
    private string|null $filename = null;
    private string|null $dir = null;
    private int $state;
    private bool $autowiring = true;

    public function __construct(
        Definitions|null $definitions = null,
    ) {
        $this->definitions = $definitions ?: new Definitions();
        $this->state = self::INITIALIZING;
    }

    public function val(
        string $id,
        string|int|float|bool|null $value,
    ): self {
        if (!$this->configurable()) {
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
        if (!$this->configurable()) {
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
        if (!$this->configurable()) {
            throw new ContainerException("Can't add definition '{$id}' as container is locked.");
        }

        $this->definitions->add($id, new Run($instantiator, ...$arguments));

        return $this;
    }

    public function ref(string $id, string $ref): self
    {
        if (!$this->configurable()) {
            throw new ContainerException("Can't add definition '{$id}' as container is locked.");
        }

        $this->definitions->add($id, new Ref($ref));

        return $this;
    }

    public function autowiring(bool $autowiring = true): self
    {
        $this->autowiring = $autowiring;

        return $this;
    }

    public function snapshot(string $dir, string $class = 'Container'): self
    {
        if ($this->state > self::INITIALIZING) {
            throw new ContainerException("Can't re-define snapshot file '{$this->filename}'.");
        }

        $this->filename = $dir . DIRECTORY_SEPARATOR . $class . '.php';
        $this->dir = $dir;
        $this->state = !file_exists($this->filename) ? self::COMPILING : self::LOADING;
        $this->class = $class;

        return $this;
    }

    public function load(Definitions|callable|array|string $configuration): self
    {
        if ($this->state === self::LOADING) {
            return $this;
        }

        if ($this->state === self::INITIALIZED) {
            throw new ContainerException("Can't load definitions when container is warm.");
        }

        if ($configuration instanceof Definitions) {
            $this->definitions->merge($configuration);

            return $this;
        }

        if (is_string($configuration)) {
            if (!file_exists($configuration)) {
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

    public function container(): ContainerInterface
    {
        if ($this->container) {
            return $this->container;
        }

        if ($this->state === self::COMPILING) {
            $compiler = new Compiler($this->definitions);
            $this->write($this->dir, $this->filename, $compiler->compile($this->class));
            $this->state = self::LOADING;
        }

        if ($this->state === self::LOADING) {
            require_once $this->filename;
            $this->state = self::INITIALIZED;

            return $this->container = $this->autowiring
                ? new Container(new Definitions(), new ($this->class)(), $this->autowiring)
                : new ($this->class)();
        }

        $this->state = self::INITIALIZED;

        return $this->container = new Container($this->definitions);
    }

    public function definitions(): Definitions
    {
        return $this->definitions;
    }

    public function configurable(): bool
    {
        return $this->state < self::LOADING;
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

    private function write(string $dir, string $filename, string $content): void
    {
        if (!is_dir($dir)) {
            if (!mkdir($dir, recursive: true) && !is_dir($dir)) {
                throw new ContainerException("Failed to create directory '{$dir}'.");
            }
        }

        if (false === $temp = tempnam($dir, basename($filename))) {
            throw new ContainerException("Failed to write temporary file to '{$dir}'.");
        }

        if (!chmod($temp, 0666)) {
            throw new ContainerException("Cannot change permissions for '{$temp}'.");
        }

        if (!file_put_contents($temp, $content)) {
            throw new ContainerException("Failed to write temporary file '{$temp}'.");
        }

        if (!rename($temp, $filename)) {
            unlink($temp);
            throw new ContainerException("Failed to rename temporary file '{$temp}' to '{$filename}'.");
        }
    }
}
