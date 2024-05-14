<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture1c1913e2;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\val;

final class CollectionTest extends BaseTestCase
{
    public function testTopLevel(): void
    {
        $container = $this->container([
            'd' => 'd',
            'collection' => [
                'a' => val('a'),
                'b' => env('MATH_PI')->float(),
                'c' => obj(stdClass::class),
                'd' => ref('d'),
            ],
        ]);

        $collection = $container->get('collection');

        $this->assertEquals('a', $collection['a']);
        $this->assertEquals(3.14, $collection['b']);
        $this->assertInstanceOf(stdClass::class, $collection['c']);
        $this->assertEquals('d', $collection['d']);
    }

    public function testNested(): void
    {
        $container = $this->container([
            'd' => 'd',
            'collection' => [
                'nested' => [
                    'a' => val('a'),
                    'b' => env('MATH_PI')->float(),
                    'c' => obj(stdClass::class),
                    'd' => ref('d'),
                ],
            ],
        ]);

        $collection = $container->get('collection')['nested'];

        $this->assertEquals('a', $collection['a']);
        $this->assertEquals(3.14, $collection['b']);
        $this->assertInstanceOf(stdClass::class, $collection['c']);
        $this->assertEquals('d', $collection['d']);
    }
}
