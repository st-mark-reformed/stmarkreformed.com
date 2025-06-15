<?php

declare(strict_types=1);

namespace App\Profiles\Profile;

use function array_map;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Profiles
{
    /** @param Profile[] $profiles */
    public function __construct(public array $profiles = [])
    {
        array_map(
            static fn (Profile $p) => $p,
            $this->profiles,
        );
    }

    /** @phpstan-ignore-next-line */
    public function mapToArray(callable $callback): array
    {
        return array_map($callback, $this->profiles);
    }

    /** @return array<array-key, array<scalar>> */
    public function asScalar(): array
    {
        return $this->mapToArray(
            static fn (Profile $profile) => $profile->asScalar(),
        );
    }
}
