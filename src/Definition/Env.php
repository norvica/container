<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

final class Env
{
    public const STRING_ =  'string';
    public const INT_ = 'int';
    public const FLOAT_ = 'float';
    public const BOOL_ = 'bool';

    private string $cast = self::STRING_;

    public function __construct(
        public readonly string $name,
        public readonly string|int|float|bool|null $default,
    ) {
    }

    public function int(): self
    {
        $this->cast = self::INT_;

        return $this;
    }

    public function float(): self
    {
        $this->cast = self::FLOAT_;

        return $this;
    }

    public function bool(): self
    {
        $this->cast = self::BOOL_;

        return $this;
    }

    public function cast(): string
    {
        return $this->cast;
    }
}
