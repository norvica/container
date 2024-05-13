<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\Factory\Factory164ee9c7;
use Tests\Norvica\Container\Fixtures\Factory\Factory1dfb210c;
use Tests\Norvica\Container\Fixtures\Factory\Factory1fe37694;
use Tests\Norvica\Container\Fixtures\Factory\Factory27172850;
use Tests\Norvica\Container\Fixtures\Factory\Factory6b883c6e;
use Tests\Norvica\Container\Fixtures\Factory\Factory6e2823de;
use Tests\Norvica\Container\Fixtures\Factory\Factory82ea8d38;
use Tests\Norvica\Container\Fixtures\Factory\Factory8b7c1a7f;
use Tests\Norvica\Container\Fixtures\Result;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\val;

final class FactoryTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('MATH_PI=3.14');
    }

    public static function configuration(): Generator
    {
        yield 'empty' => [
            [
                'factory' => obj(Factory6b883c6e::class),
                'object' => obj([ref('factory'), 'create']),
            ],
        ];

        yield 'optional (unresolvable) parameter' => [
            [
                'factory' => obj(Factory82ea8d38::class),
                'object' => obj([ref('factory'), 'create']),
            ],
        ];

        yield 'mandatory (unresolvable, explicitly passed, positional) parameter' => [
            [
                'factory' => obj(Factory164ee9c7::class),
                'object' => obj([ref('factory'), 'create'], 'a'),
            ],
        ];

        yield 'mandatory (unresolvable, explicitly passed, named) parameter' => [
            [
                'factory' => obj(Factory164ee9c7::class),
                'object' => obj([ref('factory'), 'create'], a: 'a'),
            ],
        ];

        yield 'mandatory (automatically resolvable) parameter' => [
            [
                'factory' => obj(Factory27172850::class),
                'object' => obj([ref('factory'), 'create']),
            ],
        ];

        yield 'multiple mandatory (unresolvable are explicitly passed) parameters' => [
            [
                'factory' => obj(Factory6e2823de::class),
                'object' => obj([ref('factory'), 'create'], b: 'b'),
            ],
        ];

        yield 'variadic parameter (nothing passed)' => [
            [
                'factory' => obj(Factory1fe37694::class),
                'object' => obj([ref('factory'), 'create']),
            ],
        ];

        yield 'variadic parameter' => [
            [
                'factory' => obj(Factory1fe37694::class),
                'object' => obj([ref('factory'), 'create'], b: 'b', c: 'c'),
            ],
        ];

        yield 'definitions' => [
            [
                'c' => obj(stdClass::class),
                'factory' => obj(Factory1dfb210c::class),
                'object' => obj(
                    [ref('factory'), 'create'],
                    a: val('a'),
                    b: env('MATH_PI')->float(),
                    c: ref('c'),
                ),
            ],
        ];

        yield 'nested definitions' => [
            [
                'c' => obj(stdClass::class),
                'factory' => obj(Factory8b7c1a7f::class),
                'object' => obj(
                    [ref('factory'), 'create'],
                    options: [
                        'a' => val('a'),
                        'b' => env('MATH_PI')->float(),
                        'c' => ref('c'),
                    ],
                ),
            ],
        ];
    }

    #[DataProvider('configuration')]
    public function test(array $configuration): void
    {
        $container = $this->container($configuration);

        $this->assertInstanceOf(Result::class, $container->get('object'));
    }
}
