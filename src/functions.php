<?php

declare(strict_types=1);

namespace Norvica\Container;

use Norvica\Container\Definition\Obj;
use Norvica\Container\Definition\Ref;
use Norvica\Container\Definition\Env;
use Norvica\Container\Definition\Run;
use Norvica\Container\Definition\Val;

if (!function_exists('Norvica\Container\env')) {
    function env(string $name, string|int|float|bool|null $default = null, string $type = Env::STRING_): Env
    {
        return new Env($name, $default, $type);
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

if (!function_exists('Norvica\Container\run')) {
    function run(callable|array $instantiator, mixed ...$arguments): Run
    {
        return new Run($instantiator, ...$arguments);
    }
}

if (!function_exists('Norvica\Container\val')) {
    function val(Env|array|string|int|float|bool|null $value): Val
    {
        return new Val($value);
    }
}

if (!function_exists('Norvica\Container\_env')) {
    function _env(string $name, string|int|float|bool|null $default = null, string $type = Env::STRING_): string|int|float|bool|null
    {
        if (false === $value = getenv($name)) {
            return $default;
        }

        return match ($type) {
            Env::STRING_ => $value,
            Env::INT_ => (int) $value,
            Env::FLOAT_ => (float) $value,
            Env::BOOL_ => filter_var($value, FILTER_VALIDATE_BOOL),
        };
    }
}
