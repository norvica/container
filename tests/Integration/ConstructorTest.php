<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture01dc015b;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture2a2f4b27;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture35f858f6;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture3a9ee4ab;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture5d4a6012;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture69344dda;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture82858703;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture83d4d17f;
use Tests\Norvica\Container\Fixtures\Constructor\Fixture87ad4655;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\val;

final class ConstructorTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('MATH_PI=3.14');
    }

    public static function configuration(): Generator
    {
        yield 'no constructor' => [
            ['object' => obj(Fixture5d4a6012::class)],
            Fixture5d4a6012::class,
        ];

        yield 'empty' => [
            ['object' => obj(Fixture69344dda::class)],
            Fixture69344dda::class,
        ];

        yield 'optional (unresolvable) parameter' => [
            ['object' => obj(Fixture82858703::class)],
            Fixture82858703::class,
        ];

        yield 'mandatory (unresolvable, explicitly passed, positional) parameter' => [
            ['object' => obj(Fixture87ad4655::class, 'a')],
            Fixture87ad4655::class,
        ];

        yield 'mandatory (unresolvable, explicitly passed, named) parameter' => [
            ['object' => obj(Fixture87ad4655::class, a: 'a')],
            Fixture87ad4655::class,
        ];

        yield 'mandatory (automatically resolvable) parameter' => [
            ['object' => obj(Fixture3a9ee4ab::class)],
            Fixture3a9ee4ab::class,
        ];

        yield 'multiple mandatory (unresolvable are explicitly passed) parameters' => [
            ['object' => obj(Fixture35f858f6::class, b: 'b')],
            Fixture35f858f6::class,
        ];

        yield 'variadic parameter (nothing passed)' => [
            ['object' => obj(Fixture2a2f4b27::class)],
            Fixture2a2f4b27::class,
        ];

        yield 'variadic parameter' => [
            ['object' => obj(Fixture2a2f4b27::class, b: 'b', c: 'c')],
            Fixture2a2f4b27::class,
        ];

        yield 'definitions' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(
                    Fixture01dc015b::class,
                    a: val('a'),
                    b: env('MATH_PI')->float(),
                    c: ref('c'),
                ),
            ],
            Fixture01dc015b::class,
        ];

        yield 'nested definitions' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(
                    Fixture83d4d17f::class,
                    options: [
                        'a' => val('a'),
                        'b' => env('MATH_PI')->float(),
                        'c' => ref('c'),
                    ],
                ),
            ],
            Fixture83d4d17f::class,
        ];
    }

    #[DataProvider('configuration')]
    public function test(array $configuration, string $expectation): void
    {
        $container = $this->container($configuration);

        $this->assertInstanceOf($expectation, $container->get('object'));
    }
}
