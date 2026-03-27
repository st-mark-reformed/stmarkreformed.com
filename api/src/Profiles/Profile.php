<?php

declare(strict_types=1);

namespace App\Profiles;

use App\EmptyUuid;
use Ramsey\Uuid\UuidInterface;

use function count;
use function trim;

readonly class Profile
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public string $fullName;

    public string $fullNameWithHonorific;

    public function __construct(
        public UuidInterface $id = new EmptyUuid(),
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

        $messages = ProfileValidation::validate($this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
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
     *     leadershipPositionHumanReadable: string,
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

    public function withTitleOrHonorific(string $value): self
    {
        return new self(
            id: $this->id,
            titleOrHonorific: $value,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email,
            leadershipPosition: $this->leadershipPosition,
            bio: $this->bio,
            hasMessages: $this->hasMessages,
        );
    }

    public function withFirstName(string $value): self
    {
        return new self(
            id: $this->id,
            titleOrHonorific: $this->titleOrHonorific,
            firstName: $value,
            lastName: $this->lastName,
            email: $this->email,
            leadershipPosition: $this->leadershipPosition,
            bio: $this->bio,
            hasMessages: $this->hasMessages,
        );
    }

    public function withLastName(string $value): self
    {
        return new self(
            id: $this->id,
            titleOrHonorific: $this->titleOrHonorific,
            firstName: $this->firstName,
            lastName: $value,
            email: $this->email,
            leadershipPosition: $this->leadershipPosition,
            bio: $this->bio,
            hasMessages: $this->hasMessages,
        );
    }

    public function withEmail(ProfileEmail|string $value): self
    {
        if (! $value instanceof ProfileEmail) {
            $value = new ProfileEmail(email: $value);
        }

        return new self(
            id: $this->id,
            titleOrHonorific: $this->titleOrHonorific,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $value,
            leadershipPosition: $this->leadershipPosition,
            bio: $this->bio,
            hasMessages: $this->hasMessages,
        );
    }

    public function withLeadershipPosition(
        ProfileLeadershipPosition|string $value,
    ): self {
        if (! $value instanceof ProfileLeadershipPosition) {
            $value = ProfileLeadershipPosition::fromString(type: $value);
        }

        return new self(
            id: $this->id,
            titleOrHonorific: $this->titleOrHonorific,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email,
            leadershipPosition: $value,
            bio: $this->bio,
            hasMessages: $this->hasMessages,
        );
    }

    public function withBio(string $value): self
    {
        return new self(
            id: $this->id,
            titleOrHonorific: $this->titleOrHonorific,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email,
            leadershipPosition: $this->leadershipPosition,
            bio: $value,
            hasMessages: $this->hasMessages,
        );
    }

    public function withHasMessages(bool $value): self
    {
        return new self(
            id: $this->id,
            titleOrHonorific: $this->titleOrHonorific,
            firstName: $this->firstName,
            lastName: $this->lastName,
            email: $this->email,
            leadershipPosition: $this->leadershipPosition,
            bio: $this->bio,
            hasMessages: $value,
        );
    }
}
