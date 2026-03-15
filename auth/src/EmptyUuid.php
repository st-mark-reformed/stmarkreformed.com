<?php

declare(strict_types=1);

namespace App;

use DateTimeInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification

readonly class EmptyUuid implements UuidInterface
{
    /** @inheritDoc */
    public function serialize()
    {
        throw new RuntimeException('Not implemented');
    }

    /** @inheritDoc */
    public function unserialize(string $data)
    {
        throw new RuntimeException('Not implemented');
    }

    public function getNumberConverter(): NumberConverterInterface
    {
        throw new RuntimeException('Not implemented');
    }

    /** @inheritDoc */
    public function getFieldsHex(): array
    {
        return [];
    }

    public function getClockSeqHiAndReservedHex(): string
    {
        return '';
    }

    public function getClockSeqLowHex(): string
    {
        return '';
    }

    public function getClockSequenceHex(): string
    {
        return '';
    }

    public function getDateTime(): DateTimeInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function getLeastSignificantBitsHex(): string
    {
        return '';
    }

    public function getMostSignificantBitsHex(): string
    {
        return '';
    }

    public function getNodeHex(): string
    {
        return '';
    }

    public function getTimeHiAndVersionHex(): string
    {
        return '';
    }

    public function getTimeLowHex(): string
    {
        return '';
    }

    public function getTimeMidHex(): string
    {
        return '';
    }

    public function getTimestampHex(): string
    {
        return '';
    }

    public function getVariant(): int|null
    {
        return null;
    }

    public function getVersion(): int|null
    {
        return null;
    }

    public function compareTo(UuidInterface $other): int
    {
        return 0;
    }

    public function equals(object|null $other): bool
    {
        return $other instanceof EmptyUuid;
    }

    public function getBytes(): string
    {
        throw new RuntimeException('Not implemented');
    }

    public function getFields(): FieldsInterface
    {
        throw new RuntimeException('Not implemented');
    }

    public function getHex(): Hexadecimal
    {
        throw new RuntimeException('Not implemented');
    }

    public function getInteger(): IntegerObject
    {
        throw new RuntimeException('Not implemented');
    }

    public function getUrn(): string
    {
        return '';
    }

    public function toString(): string
    {
        /** @phpstan-ignore-next-line */
        return '';
    }

    public function __toString(): string
    {
        /** @phpstan-ignore-next-line */
        return '';
    }

    public function jsonSerialize(): mixed
    {
        return '';
    }

    public function __serialize(): array
    {
        throw new RuntimeException('Not implemented');
    }

    /** @phpstan-ignore-next-line */
    public function __unserialize(array $data): void
    {
        throw new RuntimeException('Not implemented');
    }
}
