<?php

declare(strict_types=1);

namespace App\Profiles;

use App\DropdownList\DropdownListEntity;
use App\DropdownList\DropdownListItems;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

use function array_map;
use function array_values;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class Profiles implements JsonSerializable
{
    /** @var Profile[] */
    public array $profiles;

    /** @var array<string, Profile> */
    private array $byId;

    /** @param Profile[] $profiles */
    public function __construct(array $profiles)
    {
        $items = array_values(array_map(
            static fn (Profile $profile) => $profile,
            $profiles,
        ));

        $byId = [];

        foreach ($items as $profile) {
            $byId[$profile->id->toString()] = $profile;
        }

        $this->profiles = $items;
        $this->byId     = $byId;
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

    /**
     * @param callable(Profile): T $callback
     *
     * @return T[]
     *
     * @template T
     */
    public function map(callable $callback): array
    {
        return array_values(array_map(
            $callback,
            $this->profiles,
        ));
    }

    public function asDropdownList(): DropdownListItems
    {
        return new DropdownListItems(items: $this->map(
            callback: static function (Profile $profile): DropdownListEntity {
                return new DropdownListEntity(
                    value: $profile->id->toString(),
                    label: $profile->fullNameWithHonorific,
                );
            },
        ));
    }

    public function findById(UuidInterface|string $id): Profile|null
    {
        $idString = $id instanceof UuidInterface ? $id->toString() : $id;

        return $this->byId[$idString] ?? null;
    }
}
