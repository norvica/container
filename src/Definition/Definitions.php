<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

use Norvica\Container\Exception\ContainerException;

final class Definitions
{
    /**
     * @var array<string, Val|Obj|Run|Ref|Env|array>
     */
    private array $definitions = [];

    /**
     * @param array<string, Val|Obj|Run|Ref|Env|array> $definitions
     */
    public function __construct(
        array $definitions = [],
    ) {
        foreach ($definitions as $id => $definition) {
            if (!is_string($id)) {
                throw new ContainerException("Definition ID '{$id}' cannot be numeric.");
            }

            if (!($definition instanceof Val)
                && !($definition instanceof Obj)
                && !($definition instanceof Ref)
                && !($definition instanceof Env)) {
                throw new ContainerException(
                    sprintf(
                        "Definition '{$id}' must be one of the following types: '%s', '%s' given.",
                        implode("', '", [Val::class, Obj::class, Ref::class, Env::class]),
                        get_debug_type($definition),
                    )
                );
            }

            $this->add($id, $definition);
        }
    }

    public function add(string $id, Val|Obj|Run|Ref|Env|array $definition): self
    {
        $this->definitions[$id] = $definition;

        return $this;
    }

    public function get(string $id): Val|Obj|Run|Ref|Env|array
    {
        return $this->definitions[$id] ?? throw new \RuntimeException("Definition '{$id}' doesn't exist.");
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]);
    }

    public function merge(Definitions $definitions): self
    {
        $this->definitions = array_merge($this->definitions, $definitions->all());

        return $this;
    }

    /**
     * @return array<string, Val|Obj|Run|Ref|Env|array>
     */
    public function all(): array
    {
        return $this->definitions;
    }
}
