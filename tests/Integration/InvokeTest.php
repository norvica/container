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

final class InvokeTest extends BaseTestCase
{
    public static function containers(): Generator
    {
        yield 'cold' => [self::container([])];
        yield 'compiled' => [self::compiled([])];
    }

    #[DataProvider('containers')]
    public function testInvoke(InvokerInterface $container): void
    {
        [$a, $b, $c] = $container(Fixture9c429bd8::class, c: 'foo');
        $this->assertInstanceOf(Fixture289a24a0::class, $a);
        $this->assertInstanceOf(Fixture3b51a559::class, $b);
        $this->assertEquals('foo', $c);
    }

    #[DataProvider('containers')]
    public function testPositionalArguments(InvokerInterface $container): void
    {
        $this->expectException(ContainerException::class);

        $container(Fixture9c429bd8::class, 'foo');
    }

    #[DataProvider('containers')]
    public function testAutowiringDisabled(InvokerInterface $container): void
    {
        $this->expectException(ContainerException::class);

        $container(static fn() => null);
    }
}
