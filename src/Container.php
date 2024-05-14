<?php

declare(strict_types=1);

namespace Norvica\Container;

use Closure;
use Norvica\Container\Definition\Definitions;
use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Obj;
use Norvica\Container\Definition\Ref;
use Norvica\Container\Definition\Val;
use Norvica\Container\Exception\ContainerException;
use Norvica\Container\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

// TODO: compiling
// TODO: circular dependency handling
final class Container implements ContainerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $resolved = [];

    public function __construct(
        private readonly Definitions $definitions,
    ) {
    }

    /**
     * @template T
     * @param class-string<T>|string $id
     *
     * @return T
     */
    public function get(string $id): mixed
    {
        // return if already resolved
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        // if ID is a class name, try to construct it, even if it's not registered explicitly
        if (!$this->has($id)) {
            if (!class_exists($id)) {
                throw new NotFoundException("Definition '{$id}' not found.");
            }

            return $this->resolve(new Obj($id));
        }

        return $this->resolve($this->definitions->get($id));
    }

    public function has(string $id): bool
    {
        return $this->definitions->has($id);
    }

    private function resolve(mixed $definition): mixed
    {
        if (is_array($definition)) {
            return array_map(fn (mixed $item) => $this->resolve($item), $definition);
        }

        if ($definition instanceof Val) {
            return ($definition->value instanceof Env)
                ? $this->resolve($definition->value)
                : $definition->value;
        }

        if ($definition instanceof Env) {
            if (false !== ($value = getenv($definition->name))) {
                return match ($definition->cast()) {
                    Env::STRING_ => $value,
                    Env::INT_ => (int) $value,
                    Env::FLOAT_ => (float) $value,
                    Env::BOOL_ => filter_var($value, FILTER_VALIDATE_BOOL),
                };
            }

            return $definition->default;
        }

        if ($definition instanceof Ref) {
            return $this->resolved[$definition->id] ?? ($this->resolved[$definition->id] = $this->get($definition->id));
        }

        if ($definition instanceof Obj) {
            // pure class name suggests we just use a constructor
            if (is_string($definition->instantiator) && class_exists($definition->instantiator)) {
                $rc = new ReflectionClass($definition->instantiator);
                if ($rc->hasMethod('__construct')) {
                    $parameters = $this->parameters($rc->getMethod('__construct'), $definition->arguments);
                } else {
                    $parameters = [];
                }

                $instance = new ($definition->instantiator)(...$parameters);
            } else {
                // call factory method
                $closure = $this->closure($definition->instantiator);
                $parameters = $this->parameters(new ReflectionFunction($closure), $definition->arguments);
                $instance = $closure(...$parameters);
            }

            // perform defined calls on instance
            foreach ($definition->calls as $call) {
                $closure = $this->closure([$instance, $call->method]);
                $parameters = $this->parameters(new ReflectionFunction($closure), $call->arguments);
                $closure(...$parameters);
            }

            return $instance;
        }

        return $definition;
    }

    private function closure(Closure|callable|array|string $callable): Closure
    {
        // e.g. [ref(Foo::class), 'bar']
        if (is_array($callable) && $callable[0] instanceof Ref) {
            $callable[0] = $this->resolve($callable[0]);
        }

        return $callable(...);
    }

    private function parameters(ReflectionMethod|ReflectionFunction $reflection, array $arguments): array
    {
        $resolved = array_map(
            function (mixed $argument) {
                if ($argument instanceof Obj) {
                    throw new ContainerException("Nested 'obj' definitions are not supported.");
                }

                return $this->resolve($argument);
            },
            $arguments,
        );

        // TODO: attributes
        foreach ($reflection->getParameters() as $i => $rp) {
            if ($rp->isVariadic()) {
                break;
            }

            if (isset($resolved[$i]) || isset($resolved[$rp->getName()])) {
                continue;
            }

            if ($rp->isDefaultValueAvailable()) {
                continue;
            }

            $resolved[$rp->getName()] = $this->guess($rp);
        }

        return $resolved;
    }

    private function guess(ReflectionParameter $rp): mixed
    {
        // TODO: improve error handling
        $rt = $rp->getType();

        $reference = "{$rp->getDeclaringClass()?->getName()}::{$rp->getDeclaringFunction()->getName()}([\${$rp->getName()}])";

        if ($rt === null) {
            throw new ContainerException("Cannot autowire parameter {$reference} without type being defined.");
        }

        if (!$rt instanceof ReflectionNamedType) {
            throw new ContainerException("Cannot autowire parameter {$reference} based on union or intersection type.");
        }

        if ($rt->isBuiltin()) {
            throw new ContainerException("Cannot autowire parameter {$reference} based on built-in type '{$rt->getName()}'.");
        }

        return $this->get($rt->getName());
    }
}
