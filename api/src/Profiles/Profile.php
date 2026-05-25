<?php

declare(strict_types=1);

namespace App\Profiles;

use Ramsey\Uuid\UuidInterface;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

interface Profile
{
    public UuidInterface $id { get; }

    public string $titleOrHonorific { get; }

    public string $firstName { get; }

    public string $lastName { get; }

    public string $fullName { get; }

    public string $fullNameWithHonorific { get; }

    public ProfileEmail $email { get; }

    public ProfileLeadershipPosition $leadershipPosition { get; }

    public string $bio { get; }

    public bool $hasMessages { get; }

    public string $slug { get; }

    public function isEmpty(): bool;

    /**
     * @return array{
     *     id: string,
     *     titleOrHonorific: string,
     *     firstName: string,
     *     lastName: string,
     *     fullName: string,
     *     fullNameWithHonorific: string,
     *     email: string,
     *     leadershipPosition: string,
     *     leadershipPositionHumanReadable: string,
     *     bio: string,
     *     hasMessages: bool,
     * }
     */
    public function asArray(): array;
}
