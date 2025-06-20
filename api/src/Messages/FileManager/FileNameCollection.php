<?php

declare(strict_types=1);

namespace App\Messages\FileManager;

use Spatie\Cloneable\Cloneable;

use function array_map;
use function array_values;
use function count;

readonly class FileNameCollection
{
    use Cloneable;

    /** @var string[] */
    public array $names;

    /** @param string[] $names */
    public function __construct(array $names)
    {
        $this->names = array_values(array_map(
            static fn (string $name) => $name,
            $names,
        ));
    }

    public function hasNames(): bool
    {
        return $this->count() > 0;
    }

    public function has(string $name): bool
    {
        foreach ($this->names as $innerName) {
            if ($innerName !== $name) {
                continue;
            }

            return true;
        }

        return false;
    }

    public function count(): int
    {
        return count($this->names);
    }

    /** @return mixed[] */
    public function map(
        callable|null $callback = null,
    ): array {
        return array_map(
            $callback ?? static fn (string $name) => $name,
            $this->names,
        );
    }

    /** @return string[] */
    public function asScalarArray(): array
    {
        /** @phpstan-ignore-next-line */
        return $this->map(
            static fn (string $name) => $name,
        );
    }

    public function withId(string $name): FileNameCollection
    {
        $names = $this->names;

        $names[] = $name;

        return new self($names);
    }
}
