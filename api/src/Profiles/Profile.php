<?php

declare(strict_types=1);

namespace App\Profiles;

use Ramsey\Uuid\UuidInterface;

use function trim;

readonly class Profile
{
    public string $fullName;

    public string $fullNameWithHonorific;

    public function __construct(
        public UuidInterface $id,
        public string $titleOrHonorific = '',
        public string $firstName = '',
        public string $lastName = '',
        public ProfileEmail $email = new ProfileEmail(),
        public ProfileLeadershipPosition $leadershipPosition = ProfileLeadershipPosition::none,
        public string $bio = '',
        public bool $hasMessages = false,
    ) {
        $this->fullName = trim($firstName . ' ' . $lastName);

        $this->fullNameWithHonorific = trim(
            $titleOrHonorific . ' ' . $this->fullName,
        );
    }

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
     *     bio: string,
     *     hasMessages: bool,
     * }
     */
    public function asArray(): array
    {
        return [
            'id' => $this->id->toString(),
            'titleOrHonorific' => $this->titleOrHonorific,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'fullName' => $this->fullName,
            'fullNameWithHonorific' => $this->fullNameWithHonorific,
            'email' => $this->email->toString(),
            'leadershipPosition' => $this->leadershipPosition->value(),
            'leadershipPositionHumanReadable' => $this->leadershipPosition->humanReadable(),
            'bio' => $this->bio,
            'hasMessages' => $this->hasMessages,
        ];
    }
}
