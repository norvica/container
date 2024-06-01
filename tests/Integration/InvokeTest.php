<?php

declare(strict_types=1);

namespace Tests\Norvica\Container\Integration;

use Generator;
use Norvica\Container\Exception\ContainerException;
use Norvica\Container\InvokerInterface;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Norvica\Container\BaseTestCase;
use Tests\Norvica\Container\Fixtures\Invoke\Fixture289a24a0;
use Tests\Norvica\Container\Fixtures\Invoke\Fixture3b51a559;
use Tests\Norvica\Container\Fixtures\Invoke\Fixture9c429bd8;
use function Norvica\Container\ref;

final class InvokeTest extends BaseTestCase
{
    public static function containers(): Generator
    {
        yield 'cold container' => [self::container([])];
        yield 'compiled container' => [self::compiled([])];
    }

    #[DataProvider('containers')]
    public function testInvokeWithNamedArguments(InvokerInterface $container): void
    {
        [$a, $b, $c] = $container(Fixture9c429bd8::class, c: 'foo');
        $this->assertInstanceOf(Fixture289a24a0::class, $a);
        $this->assertInstanceOf(Fixture3b51a559::class, $b);
        $this->assertEquals('foo', $c);
    }

    #[DataProvider('containers')]
    public function testInvokeWithPositionalArguments(InvokerInterface $container): void
    {
        [$a, $b, $c] = $container(Fixture9c429bd8::class, ref(Fixture3b51a559::class), 'foo');
        $this->assertInstanceOf(Fixture289a24a0::class, $a);
        $this->assertInstanceOf(Fixture3b51a559::class, $b);
        $this->assertEquals('foo', $c);
    }

    public function testAutowiringDisabledCold(): void
    {
        $container = self::container([], false);
        $this->expectException(ContainerException::class);

        $container(static fn() => null);
    }

    public function testAutowiringDisabledCompiled(): void
    {
        $container = self::compiled([], false);
        $this->expectException(ContainerException::class);

        $container(static fn() => null);
    }
}
