<?php

declare(strict_types=1);

namespace App\Profiles;

use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_find;
use function array_map;
use function array_values;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Profiles implements JsonSerializable
{
    /** @var Profile[] */
    public array $profiles;

    /** @param Profile[] $profiles */
    public function __construct(array $profiles)
    {
        $this->profiles = array_values(array_map(
            static fn (Profile $profile) => $profile,
            $profiles,
        ));
    }

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
    }

    /**
     * @return array<array-key, array{
     *     id: string,
     *     titleOrHonorific: string,
     *     firstName: string,
     *     lastName: string,
     *     fullName: string,
     *     fullNameWithHonorific: string,
     *     email: string,
     *     leadershipPosition: string,
     *     bio: string,
     *     hasMessages: bool,
     * }>
     */
    public function asArray(): array
    {
        return array_map(
            static fn (Profile $profile) => $profile->asArray(),
            $this->profiles,
        );
    }

    public function findById(UuidInterface|string $id): Profile|null
    {
        $id = $id instanceof UuidInterface ? $id->toString() : $id;

        return array_find(
            $this->profiles,
            static fn (Profile $profile) => $profile->id->toString() === $id,
        );
    }
}
