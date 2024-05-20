<?php

declare(strict_types=1);

namespace Norvica\Container\Compiler;

use PhpParser\Node;
use PhpParser\NodeVisitor;

final class NodeTraversal
{
    public static function traverse(Node|array $subject, callable $callback): void
    {
        // breadth first
        $queue = [$subject];

        while (!empty($queue)) {
            $current = array_shift($queue);

            if ($current instanceof Node) {
                $result = $callback($current);
                if ($result === NodeVisitor::DONT_TRAVERSE_CHILDREN) {
                    continue;
                }

                // enqueue sub-nodes for further traversal
                foreach ($current->getSubNodeNames() as $name) {
                    $subNode = $current->{$name};
                    if ($subNode instanceof Node || is_array($subNode)) {
                        $queue[] = $subNode;
                    }
                }
            } elseif (is_array($current)) {
                // enqueue elements from the array for further traversal
                foreach ($current as $item) {
                    if ($item instanceof Node || is_array($item)) {
                        $queue[] = $item;
                    }
                }
            }
        }
    }
}
