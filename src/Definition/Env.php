<?php

declare(strict_types=1);

namespace Norvica\Container\Definition;

use Attribute;
use UnexpectedValueException;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class Env
{
    public const STRING_ =  'string';
    public const INT_ = 'int';
    public const FLOAT_ = 'float';
    public const BOOL_ = 'bool';

    private string $type;

    /**
     * @param Env::* $type
     */
    public function __construct(
        public readonly string $name,
        public readonly string|int|float|bool|null $default = null,
        string $type = self::STRING_,
    ) {
        $this->type = self::canonicalize($type);
    }

    public function int(): self
    {
        $this->type = self::INT_;

        return $this;
    }

    public function float(): self
    {
        $this->type = self::FLOAT_;

        return $this;
    }

    public function bool(): self
    {
        $this->type = self::BOOL_;

        return $this;
    }

    public function type(): string
    {
        return $this->type;
    }

    private static function canonicalize(string $type): string
    {
        return match (strtolower($type)) {
            self::STRING_ => self::STRING_,
            'integer', self::INT_ => self::INT_,
            self::FLOAT_ => self::FLOAT_,
            'boolean', self::BOOL_ => self::BOOL_,
            default => throw new UnexpectedValueException(
                sprintf(
                    "Unexpected type '{$type}' given. Valid types are: '%s'.",
                    implode("', '", [self::STRING_, self::INT_, self::FLOAT_, self::BOOL_]),
                )
            ),
        };
    }
}
