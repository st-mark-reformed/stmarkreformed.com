<?php

declare(strict_types=1);

namespace App\Http\Shared\ValueObjects;

use Stringable;

class StringValue implements Stringable
{
    public function __construct(private string $value)
    {
    }

    public function __toString(): string
    {
        return $this->toString();
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function hasValue(): bool
    {
        return $this->value !== '';
    }

    public function hasNoValue(): bool
    {
        return $this->value === '';
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
