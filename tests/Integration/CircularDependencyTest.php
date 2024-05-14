<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use Norvica\Container\Exception\CircularDependencyException;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\CircularDependency\A;
use Tests\Norvica\Container\Fixtures\CircularDependency\B;
use Tests\Norvica\Container\Fixtures\CircularDependency\C;
use Tests\Norvica\Container\Fixtures\CircularDependency\D;
use Tests\Norvica\Container\Fixtures\CircularDependency\E;
use function Norvica\Container\obj;
use function Norvica\Container\ref;

final class CircularDependencyTest extends BaseTestCase
{
    public static function configuration(): Generator
    {
        yield 'direct dependency, implicit definition' => [[], A::class];

        yield 'direct dependency, explicit definition' => [[
            'a' => obj(A::class, ref('b')),
            'b' => obj(B::class, ref('a')),
        ], 'a'];

        yield 'proxy dependency, implicit definition' => [[], C::class];

        yield 'proxy dependency, explicit definition' => [[
            'c' => obj(C::class, ref('d')),
            'd' => obj(D::class, ref('e')),
            'e' => obj(E::class, ref('c')),
        ], 'c'];
    }

    #[DataProvider('configuration')]
    public function test(array $configuration, string $id): void
    {
        $this->expectException(CircularDependencyException::class);
        $container = $this->container($configuration);

        $container->get($id);
    }
}
