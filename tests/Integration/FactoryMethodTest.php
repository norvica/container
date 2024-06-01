<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture04149d77;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture0c18ad61;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture0fd8a6ce;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture1c1913e2;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture2b3c6dca;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture426d9e13;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture4a941956;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture5cb29c3f;
use Tests\Norvica\Container\Fixtures\FactoryMethod\Fixture6df9fb69;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\val;

final class FactoryMethodTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('MATH_PI=3.14');
    }

    public static function configuration(): Generator
    {
        yield 'empty' => [
            ['object' => obj(Fixture426d9e13::create(...))],
            Fixture426d9e13::class,
        ];

        yield 'optional (unresolvable) parameter' => [
            ['object' => obj(Fixture04149d77::create(...))],
            Fixture04149d77::class,
        ];

        yield 'mandatory (unresolvable, explicitly passed, positional) parameter' => [
            ['object' => obj(Fixture0c18ad61::create(...), 'a')],
            Fixture0c18ad61::class,
        ];

        yield 'mandatory (unresolvable, explicitly passed, named) parameter' => [
            ['object' => obj(Fixture0c18ad61::create(...), a: 'a')],
            Fixture0c18ad61::class,
        ];

        yield 'mandatory (automatically resolvable) parameter' => [
            ['object' => obj(Fixture4a941956::create(...))],
            Fixture4a941956::class,
        ];

        yield 'multiple mandatory (unresolvable are explicitly passed) parameters' => [
            ['object' => obj(Fixture2b3c6dca::create(...), b: 'b')],
            Fixture2b3c6dca::class,
        ];

        yield 'variadic parameter (nothing passed)' => [
            ['object' => obj(Fixture5cb29c3f::create(...))],
            Fixture5cb29c3f::class,
        ];

        yield 'variadic parameter' => [
            ['object' => obj(Fixture5cb29c3f::create(...), b: 'b', c: 'c')],
            Fixture5cb29c3f::class,
        ];

        yield 'definitions' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(
                    Fixture6df9fb69::create(...),
                    a: val('a'),
                    b: env('MATH_PI')->float(),
                    c: ref('c'),
                ),
            ],
            Fixture6df9fb69::class,
        ];

        yield 'nested definitions' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(
                    Fixture1c1913e2::create(...),
                    options: [
                        'a' => val('a'),
                        'b' => env('MATH_PI')->float(),
                        'c' => ref('c'),
                    ],
                ),
            ],
            Fixture1c1913e2::class,
        ];

        yield 'attributes' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(Fixture0fd8a6ce::create(...)),
            ],
            Fixture0fd8a6ce::class,
        ];
    }

    #[DataProvider('configuration')]
    public function test(array $configuration, string $expectation): void
    {
        $container = self::container($configuration);
        $this->assertInstanceOf($expectation, $container->get('object'));

        $compiled = self::compiled($configuration);
        $this->assertInstanceOf($expectation, $compiled->get('object'));
    }
}
