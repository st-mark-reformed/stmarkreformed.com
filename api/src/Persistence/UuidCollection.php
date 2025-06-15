<?php

declare(strict_types=1);

namespace App\Persistence;

use Ramsey\Uuid\UuidInterface;
use Spatie\Cloneable\Cloneable;

use function array_map;
use function array_values;
use function count;

readonly class UuidCollection
{
    use Cloneable;

    /** @var UuidInterface[] */
    private array $ids;

    /** @param UuidInterface[] $ids */
    public function __construct(array $ids)
    {
        $this->ids = array_values(array_map(
            static fn (UuidInterface $id) => $id,
            $ids,
        ));
    }

    public function hasIds(): bool
    {
        return $this->count() > 0;
    }

    public function has(UuidInterface $id): bool
    {
        foreach ($this->ids as $innerId) {
            if ($innerId->toString() !== $id->toString()) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function count(): int
    {
        return count($this->ids);
    }

    /** @return mixed[] */
    public function map(
        callable|null $callback = null,
    ): array {
        return array_map(
            $callback ?? static fn (UuidInterface $id) => $id,
            $this->ids,
        );
    }

    /** @return string[] */
    public function asScalarArray(): array
    {
        /** @phpstan-ignore-next-line */
        return $this->map(
            static fn (UuidInterface $id) => $id->toString(),
        );
    }

    public function withId(UuidInterface $id): UuidCollection
    {
        $ids = $this->ids;

        $ids[] = $id;

        return new self($ids);
    }
}
