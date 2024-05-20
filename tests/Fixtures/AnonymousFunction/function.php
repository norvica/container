<?php

declare(strict_types=1);

use Norvica\Container\Configurator;
use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Ref;

return static function (Configurator $configurator) {
    $configurator->run('a', static fn() => 'foo');
    $configurator->run('b', fn() => 'bar');
    $configurator->run('c', static function (#[Ref('a')] $a, #[Ref('b')] $b) {
        return "{$a}{$b}";
    });
    $configurator->run('d', function (#[Env('_A', 'a')] $a, #[Env('_B', 'b')] $b) {
        return "{$a}{$b}";
    });
    $configurator->obj('e', function (#[Ref('a')] $a, $b) {
        $obj = new stdClass();
        $obj->a = $a;
        $obj->b = $b;

        return $obj;
    }, b: \Norvica\Container\ref('b'));
};
