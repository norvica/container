<?php

declare(strict_types=1);

namespace Norvica\Container;

use Norvica\Container\Exception\CircularDependencyException;
use Norvica\Container\Exception\ContainerException;

/**
 * @internal
 */
final class Visitor
{
    /**
     * @var string[]
     */
    private array $visiting = [];

    public function enter(string $id): void
    {
        if ($this->visiting($id)) {
            throw new CircularDependencyException(
                sprintf(
                    "Circular dependency detected when resolving the following chain: '%s' → '{$id}'.",
                    implode("' → '", $this->visiting),
                )
            );
        }

        $this->visiting[] = $id;
    }

    public function exit(string $id): void
    {
        $index = array_search($id, $this->visiting, true);
        if ($index === false) {
            throw new ContainerException("Tried to exit node '{$id}' that hasn't been entered.");
        }

        unset($this->visiting[$index]);
    }

    private function visiting(string $id): bool
    {
        return in_array($id, $this->visiting, true);
    }
}
