<?php

declare(strict_types=1);

namespace App\Profiles;

use function array_map;
use function array_values;

readonly class Profiles
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
}
