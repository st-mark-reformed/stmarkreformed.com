<?php

declare(strict_types=1);

namespace App;

use DateTimeInterface;
use Ramsey\Uuid\Converter\NumberConverterInterface;
use Ramsey\Uuid\Fields\FieldsInterface;
use Ramsey\Uuid\Type\Hexadecimal;
use Ramsey\Uuid\Type\Integer as IntegerObject;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification
// phpcs:disable SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingTraversableTypeHintSpecification

readonly class EmptyUuid implements UuidInterface
{
    private UuidInterface $uuid;

    public function __construct()
    {
        $this->uuid = Uuid::fromString(
            '00000000-0000-0000-0000-000000000000',
        );
    }

    public function serialize(): string
    {
        return $this->uuid->serialize();
    }

    public function unserialize(string $data): void
    {
        $this->uuid->unserialize($data);
    }

    public function getNumberConverter(): NumberConverterInterface
    {
        return $this->uuid->getNumberConverter();
    }

    /** @inheritDoc */
    public function getFieldsHex(): array
    {
        return $this->uuid->getFieldsHex();
    }

    public function getClockSeqHiAndReservedHex(): string
    {
        return $this->uuid->getClockSeqHiAndReservedHex();
    }

    public function getClockSeqLowHex(): string
    {
        return $this->uuid->getClockSeqLowHex();
    }

    public function getClockSequenceHex(): string
    {
        return $this->uuid->getClockSequenceHex();
    }

    public function getDateTime(): DateTimeInterface
    {
        return $this->uuid->getDateTime();
    }

    public function getLeastSignificantBitsHex(): string
    {
        return $this->uuid->getLeastSignificantBitsHex();
    }

    public function getMostSignificantBitsHex(): string
    {
        return $this->uuid->getMostSignificantBitsHex();
    }

    public function getNodeHex(): string
    {
        return $this->uuid->getNodeHex();
    }

    public function getTimeHiAndVersionHex(): string
    {
        return $this->uuid->getTimeHiAndVersionHex();
    }

    public function getTimeLowHex(): string
    {
        return $this->uuid->getTimeLowHex();
    }

    public function getTimeMidHex(): string
    {
        return $this->uuid->getTimeMidHex();
    }

    public function getTimestampHex(): string
    {
        return $this->uuid->getTimestampHex();
    }

    public function getVariant(): int|null
    {
        return $this->uuid->getVariant();
    }

    public function getVersion(): int|null
    {
        return $this->uuid->getVersion();
    }

    public function compareTo(UuidInterface $other): int
    {
        return $this->uuid->compareTo($other);
    }

    public function equals(object|null $other): bool
    {
        return $other instanceof EmptyUuid;
    }

    public function getBytes(): string
    {
        return $this->uuid->getBytes();
    }

    public function getFields(): FieldsInterface
    {
        return $this->uuid->getFields();
    }

    public function getHex(): Hexadecimal
    {
        return $this->uuid->getHex();
    }

    public function getInteger(): IntegerObject
    {
        return $this->uuid->getInteger();
    }

    public function getUrn(): string
    {
        return $this->uuid->getUrn();
    }

    public function toString(): string
    {
        return $this->uuid->toString();
    }

    public function __toString(): string
    {
        return $this->uuid->toString();
    }

    public function jsonSerialize(): mixed
    {
        return $this->uuid->jsonSerialize();
    }

    public function __serialize(): array
    {
        /** @phpstan-ignore-next-line */
        return $this->uuid->__serialize();
    }

    /** @phpstan-ignore-next-line */
    public function __unserialize(array $data): void
    {
        $this->uuid->__unserialize($data);
    }
}
