<?php

declare(strict_types=1);

namespace App\Profiles\Profile;

use function array_filter;
use function array_map;
use function array_merge;
use function array_values;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Profiles
{
    /** @var Profile[] $profiles */
    public array $profiles;

    /** @param Profile[] $profiles */
    public function __construct(array $profiles = [])
    {
        $this->profiles = array_values(array_map(
            static fn (Profile $p) => $p,
            $profiles,
        ));
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

    public function filter(callable $callback): Profiles
    {
        return new Profiles(array_values(array_filter(
            $this->profiles,
            $callback,
        )));
    }

    public function findFirst(): Profile|null
    {
        return $this->profiles[0] ?? null;
    }

    public function findById(string $id): Profile|null
    {
        return $this->filter(
            static fn (Profile $p) => $p->id->toString() === $id,
        )->findFirst();
    }

    public function withAddedProfile(Profile|null $profile): Profiles
    {
        if ($profile === null) {
            return $this;
        }

        return new Profiles(array_merge(
            $this->profiles,
            [$profile],
        ));
    }

    public function withAddedProfiles(Profiles $profiles): Profiles
    {
        return new Profiles(array_merge(
            $this->profiles,
            $profiles->profiles,
        ));
    }
}
