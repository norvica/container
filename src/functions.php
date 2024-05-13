<?php

declare(strict_types=1);

namespace Norvica\Container;

use Norvica\Container\Definition\Obj;
use Norvica\Container\Definition\Ref;
use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Val;

if (!function_exists('Norvica\Container\env')) {
    function env(string $name, string|int|float|bool|null $default = null): Env
    {
        return new Env($name, $default);
    }
}

if (!function_exists('Norvica\Container\ref')) {
    function ref(string $id): Ref
    {
        return new Ref($id);
    }
}

if (!function_exists('Norvica\Container\obj')) {
    function obj(callable|array|string $instantiator, mixed ...$arguments): Obj
    {
        return new Obj($instantiator, ...$arguments);
    }
}

if (!function_exists('Norvica\Container\val')) {
    function val(Env|array|string|int|float|bool|null $value): Val
    {
        return new Val($value);
    }
}
