<?php

declare(strict_types=1);

use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;
use function Norvica\Container\obj;

return [
    'a' => static fn() => 'foo',
    'b' => fn() => 'bar',
    'c' => static function (#[Ref('a')] $a, #[Ref('b')] $b) {return "{$a}{$b}";},
    'd' => function (#[Env('_A', 'a')] $a, #[Env('_B', 'b')] $b) {return "{$a}{$b}";},
    'e' => obj(function (#[Ref('a')] $a, $b) {
        $obj = new stdClass();
        $obj->a = $a;
        $obj->b = $b;

        return $obj;
    }, b: \Norvica\Container\ref('b')),
];
