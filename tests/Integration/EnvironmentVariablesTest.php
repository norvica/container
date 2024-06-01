<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Container\BaseTestCase;
use function Norvica\Container\env;

final class EnvironmentVariablesTest extends BaseTestCase
{
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        putenv('STRING_PARAM=foo');
        putenv('INT_PARAM=2');
        putenv('FLOAT_PARAM=3.14');
        putenv('BOOL_PARAM=false');
    }

    public static function configuration(): Generator
    {
        yield 'string' => [['value' => env('STRING_PARAM')], ['value', 'foo']];
        yield 'string with default' => [['value' => env('STRING_PARAM_DEFAULT', 'foo')], ['value', 'foo']];
        yield 'integer' => [['value' => env('INT_PARAM')->int()], ['value', 2]];
        yield 'integer with default' => [['value' => env('INT_PARAM_DEFAULT', 2)->int()], ['value', 2]];
        yield 'float' => [['value' => env('FLOAT_PARAM')->float()], ['value', 3.14]];
        yield 'float with default' => [['value' => env('FLOAT_PARAM_DEFAULT', 3.14)->float()], ['value', 3.14]];
        yield 'bool' => [['value' => env('BOOL_PARAM')->bool()], ['value', false]];
        yield 'bool with default' => [['value' => env('BOOL_PARAM_DEFAULT', false)->bool()], ['value', false]];
    }

    #[DataProvider('configuration')]
    public function testCold(array $configuration, array $expectation): void
    {
        [$id, $value] = $expectation;
        $container = self::container($configuration);
        $this->assertEquals($value, $container->get($id));

        $compiled = self::compiled($configuration);
        $this->assertEquals($value, $compiled->get($id));
    }
}
