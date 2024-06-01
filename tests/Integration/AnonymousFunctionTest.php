<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\DataProvider;
use stdClass;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\Result;
use function Norvica\Container\env;
use function Norvica\Container\obj;
use function Norvica\Container\ref;
use function Norvica\Container\val;

final class AnonymousFunctionTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('MATH_PI=3.14');
    }

    public static function configuration(): Generator
    {
        yield 'empty' => [[
            'object' => static function () {
                return new Result();
            },
        ]];

        yield 'optional (unresolvable) parameter' => [[
            'object' => static function (string $a = 'a') {
                Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

                return new Result();
            },
        ]];

        yield 'mandatory (unresolvable, explicitly passed, positional) parameter' => [[
            'object' => obj(static function (string $a) {
                Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

                return new Result();
            }, 'a'),
        ]];

        yield 'mandatory (unresolvable, explicitly passed, named) parameter' => [[
            'object' => obj(static function (string $a) {
                Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");

                return new Result();
            }, a: 'a'),
        ]];

        yield 'mandatory (automatically resolvable) parameter' => [[
            'object' => static function (stdClass $a) {
                return new Result();
            },
        ]];

        yield 'multiple mandatory (unresolvable are explicitly passed) parameters' => [[
            'object' => obj(static function (stdClass $a, string $b) {
                Assert::assertEquals('b', $b, "Failed asserting parameter \$b equals 'b'.");

                return new Result();
            }, b: 'b'),
        ]];

        yield 'variadic parameter (nothing passed)' => [[
            'object' => static function (string ...$a) {
                Assert::assertEmpty($a);

                return new Result();
            },
        ]];

        yield 'variadic parameter' => [[
            'object' => obj(static function (string ...$a) {
                Assert::assertEquals(['b' => 'b', 'c' => 'c'], $a);

                return new Result();
            }, b: 'b', c: 'c'),
        ]];

        yield 'definitions' => [[
            'c' => obj(stdClass::class),
            'object' => obj(
                static function (string $a, float $b, stdClass $c) {
                    Assert::assertEquals('a', $a, "Failed asserting parameter \$a equals 'a'.");
                    Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");

                    return new Result();
                },
                a: val('a'),
                b: env('MATH_PI')->float(),
                c: ref('c'),
            ),
        ]];

        yield 'nested definitions' => [
            [
                'c' => obj(stdClass::class),
                'object' => obj(
                    static function (array $options) {
                        Assert::assertEquals('a', $options['a'], "Failed asserting option 'a' equals 'a'.");
                        Assert::assertEquals(3.14, $options['b'], "Failed asserting option 'b' equals 3.14.");
                        Assert::assertInstanceOf(stdClass::class, $options['c'], "Failed asserting option 'c' is an instance of \stdClass.");

                        return new Result();
                    },
                    options: [
                        'a' => val('a'),
                        'b' => env('MATH_PI')->float(),
                        'c' => ref('c'),
                    ],
                ),
            ],
        ];

        yield 'attributes' => [[
            'c' => obj(stdClass::class),
            'object' => static function (
                #[Env('MATH_PI', type: 'float')] $b,
                #[Ref('c')] $c,
            ) {
                Assert::assertEquals(3.14, $b, "Failed asserting parameter \$b equals 3.14.");
                Assert::assertInstanceOf(stdClass::class, $c, "Failed asserting parameter \$c is instance of \stdClass.");

                return new Result();
            },
        ]];
    }

    #[DataProvider('configuration')]
    public function testCold(array $configuration): void
    {
        $container = self::container($configuration);
        $this->assertInstanceOf(Result::class, $container->get('object'));

        $compiled = self::compiled($configuration);
        $this->assertInstanceOf(Result::class, $compiled->get('object'));
    }

    public static function files(): Generator
    {
        yield 'array' => [__DIR__ . '/../Fixtures/AnonymousFunction/array.php'];
        yield 'function' => [__DIR__ . '/../Fixtures/AnonymousFunction/function.php'];
    }

    #[DataProvider('files')]
    public function testCompiled(string $file): void
    {
        $compiled = self::compiled($file);

        $this->assertEquals('foo', $compiled->get('a'));
        $this->assertEquals('bar', $compiled->get('b'));
        $this->assertEquals('foobar', $compiled->get('c'));
        $this->assertEquals('ab', $compiled->get('d'));

        $e = $compiled->get('e');
        $this->assertInstanceOf(stdClass::class, $e);
        $this->assertEquals('foo', $e->a);
        $this->assertEquals('bar', $e->b);
    }
}
