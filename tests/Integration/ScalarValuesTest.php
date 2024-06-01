<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Container\BaseTestCase;
use function Norvica\Container\val;

final class ScalarValuesTest extends BaseTestCase
{
    public static function configuration(): Generator
    {
        yield 'string (implicit)' => [['value' => 'foo'], ['value', 'foo']];
        yield 'string (explicit)' => [['value' => val('foo')], ['value', 'foo']];
        yield 'negative integer (implicit)' => [['value' => PHP_INT_MIN], ['value', PHP_INT_MIN]];
        yield 'negative integer (explicit)' => [['value' => val(PHP_INT_MIN)], ['value', PHP_INT_MIN]];
        yield 'positive integer (implicit)' => [['value' => PHP_INT_MAX], ['value', PHP_INT_MAX]];
        yield 'positive integer (explicit)' => [['value' => val(PHP_INT_MAX)], ['value', PHP_INT_MAX]];
        yield 'negative float (implicit)' => [['value' => PHP_FLOAT_MIN], ['value', PHP_FLOAT_MIN]];
        yield 'negative float (explicit)' => [['value' => val(PHP_FLOAT_MIN)], ['value', PHP_FLOAT_MIN]];
        yield 'positive float (implicit)' => [['value' => PHP_FLOAT_MAX], ['value', PHP_FLOAT_MAX]];
        yield 'positive float (explicit)' => [['value' => val(PHP_FLOAT_MAX)], ['value', PHP_FLOAT_MAX]];
        yield 'boolean true (implicit)' => [['value' => true], ['value', true]];
        yield 'boolean true (explicit)' => [['value' => val(true)], ['value', true]];
        yield 'boolean false (implicit)' => [['value' => false], ['value', false]];
        yield 'boolean false (explicit)' => [['value' => val(false)], ['value', false]];
        yield 'null (implicit)' => [['value' => null], ['value', null]];
        yield 'null (explicit)' => [['value' => val(null)], ['value', null]];
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
