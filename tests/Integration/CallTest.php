<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\Call\Fixture0c7e1d40;
use Tests\Norvica\Container\Fixtures\Call\Fixture39c3f3fc;
use Tests\Norvica\Container\Fixtures\Call\Fixture43eb7e62;
use Tests\Norvica\Container\Fixtures\Call\Fixture578dc31f;
use Tests\Norvica\Container\Fixtures\Call\Fixture7ac9f19d;
use Tests\Norvica\Container\Fixtures\Call\Fixture884de825;
use Tests\Norvica\Container\Fixtures\Call\Fixture89120f9d;
use Tests\Norvica\Container\Fixtures\Call\Fixture9127af36;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\val;

final class CallTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('MATH_PI=3.14');
    }

    public static function configuration(): Generator
    {
        yield 'optional (unresolvable) parameter' => [
            ['object' => obj(Fixture7ac9f19d::class)->call('set')],
            Fixture7ac9f19d::class,
        ];

        yield 'mandatory (unresolvable, explicitly passed, positional) parameter' => [
            ['object' => obj(Fixture884de825::class)->call('set', 'a')],
            Fixture884de825::class,
        ];

        yield 'mandatory (unresolvable, explicitly passed, named) parameter' => [
            ['object' => obj(Fixture884de825::class)->call('set', a: 'a')],
            Fixture884de825::class,
        ];

        yield 'mandatory (automatically resolvable) parameter' => [
            ['object' => obj(Fixture0c7e1d40::class)->call('set')],
            Fixture0c7e1d40::class,
        ];

        yield 'multiple mandatory (unresolvable are explicitly passed) parameters' => [
            ['object' => obj(Fixture89120f9d::class)->call('set', b: 'b')],
            Fixture89120f9d::class,
        ];

        yield 'variadic parameter (nothing passed)' => [
            ['object' => obj(Fixture9127af36::class)->call('set')],
            Fixture9127af36::class,
        ];

        yield 'variadic parameter' => [
            ['object' => obj(Fixture9127af36::class)->call('set', b: 'b', c: 'c')],
            Fixture9127af36::class,
        ];

        yield 'definitions' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(Fixture578dc31f::class)
                    ->call('setA', val('a'))
                    ->call('setB', env('MATH_PI')->float())
                    ->call('setC', ref('c')),
            ],
            Fixture578dc31f::class,
        ];

        yield 'nested definitions' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(Fixture39c3f3fc::class)
                    ->call('set', options: [
                        'a' => val('a'),
                        'b' => env('MATH_PI')->float(),
                        'c' => ref('c'),
                    ]),
            ],
            Fixture39c3f3fc::class,
        ];

        yield 'attributes' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(Fixture43eb7e62::class)
                    ->call('setB')
                    ->call('setC'),
            ],
            Fixture43eb7e62::class,
        ];
    }

    #[DataProvider('configuration')]
    public function test(array $configuration, string $expectation): void
    {
        $container = $this->container($configuration);

        $this->assertInstanceOf($expectation, $container->get('object'));
    }
}
