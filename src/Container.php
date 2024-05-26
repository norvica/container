<?php

declare(strict_types=1);

namespace Norvica\Container;

use Closure;
use Norvica\Container\Definition\Definitions;
use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Obj;
use Norvica\Container\Definition\Ref;
use Norvica\Container\Definition\Run;
use Norvica\Container\Definition\Val;
use Norvica\Container\Exception\ContainerException;
use Norvica\Container\Exception\NotFoundException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use ReflectionNamedType;
use ReflectionParameter;

/**
 * @internal
 */
final class Container implements ContainerInterface
{
    /**
     * @var array<string, mixed>
     */
    private array $resolved = [];

    private Visitor $visitor;

    public function __construct(
        private readonly Definitions $definitions,
        private readonly ContainerInterface|null $compiled = null,
        private readonly bool $autowiring = true,
    ) {
        $this->visitor = new Visitor();
    }

    /**
     * @template T
     * @param class-string<T>|string $id
     *
     * @return T|mixed
     *
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function get(string $id): mixed
    {
        // return if already resolved
        if (isset($this->resolved[$id])) {
            return $this->resolved[$id];
        }

        if ($this->compiled?->has($id)) {
            return $this->compiled->get($id);
        }

        $this->visitor->enter($id);

        // if ID is a class name, try to construct it, even if it's not registered explicitly
        if (!$this->definitions->has($id) && $this->autowiring) {
            if (!class_exists($id)) {
                throw new NotFoundException("Definition '{$id}' not found.");
            }

            $resolved = $this->resolve(new Obj($id));
            $this->resolved[$id] = $resolved;
            $this->visitor->exit($id);

            return $resolved;
        }

        $resolved = $this->resolve($this->definitions->get($id));
        $this->resolved[$id] = $resolved;
        $this->visitor->exit($id);

        return $resolved;
    }

    public function has(string $id): bool
    {
        return $this->definitions->has($id) || $this->compiled?->has($id);
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
                return match ($definition->type()) {
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
                if ($this->autowiring) {
                    $rc = new ReflectionClass($definition->instantiator);
                    if ($rc->hasMethod('__construct')) {
                        $parameters = $this->parameters(
                            $definition->arguments,
                            $rc->getMethod('__construct'),
                        );
                    } else {
                        $parameters = [];
                    }
                } else {
                    $parameters = $this->parameters($definition->arguments);
                }

                $instance = new ($definition->instantiator)(...$parameters);
            } else {
                // call factory method
                $closure = $this->closure($definition->instantiator);
                $parameters = $this->parameters(
                    $definition->arguments,
                    $this->autowiring ? new ReflectionFunction($closure) : null,
                );
                $instance = $closure(...$parameters);
            }

            // perform defined calls on instance
            foreach ($definition->calls as $call) {
                $closure = $this->closure([$instance, $call->method]);
                $parameters = $this->parameters(
                    $call->arguments,
                    $this->autowiring ? new ReflectionFunction($closure) : null,
                );
                $closure(...$parameters);
            }

            return $instance;
        }

        if ($definition instanceof Run) {
            // execute closure
            $closure = $this->closure($definition->instantiator);
            $parameters = $this->parameters(
                $definition->arguments,
                $this->autowiring ? new ReflectionFunction($closure) : null,
            );

            return $closure(...$parameters);
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

    private function parameters(array $arguments, ReflectionMethod|ReflectionFunction|null $reflection = null): array
    {
        $resolved = array_map($this->resolve(...), $arguments);

        if ($reflection !== null) {
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
        }

        return $resolved;
    }

    private function guess(ReflectionParameter $rp): mixed
    {
        if (null !== $ref = ($rp->getAttributes(Ref::class)[0] ?? null)) {
            return $this->resolve($ref->newInstance());
        }

        if (null !== $env = ($rp->getAttributes(Env::class)[0] ?? null)) {
            return $this->resolve($env->newInstance());
        }

        $rt = $rp->getType();

        $reference = "'\${$rp->getName()}' in '{$rp->getDeclaringClass()?->getName()}::{$rp->getDeclaringFunction()->getName()}()'";

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
