<?php

declare(strict_types=1);

namespace App\Profiles;

use App\EmptyUuid;
use JsonSerializable;
use Ramsey\Uuid\UuidInterface;

// phpcs:disable SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingTraversableTypeHintSpecification

readonly class EmptyProfile implements Profile, JsonSerializable
{
    public UuidInterface $id;

    public string $titleOrHonorific;

    public string $firstName;

    public string $lastName;

    public string $fullName;

    public string $fullNameWithHonorific;

    public ProfileEmail $email;

    public ProfileLeadershipPosition $leadershipPosition;

    public string $bio;

    public bool $hasMessages;

    public string $slug;

    public function __construct()
    {
        $this->id                    = new EmptyUuid();
        $this->titleOrHonorific      = '';
        $this->firstName             = '';
        $this->lastName              = '';
        $this->fullName              = '';
        $this->fullNameWithHonorific = '';
        $this->email                 = new ProfileEmail();
        $this->leadershipPosition    = ProfileLeadershipPosition::none;
        $this->bio                   = '';
        $this->hasMessages           = false;
        $this->slug                  = '';
    }

    public function isEmpty(): bool
    {
        return true;
    }

    /** @phpstan-ignore-next-line */
    public function jsonSerialize(): array
    {
        return $this->asArray();
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
}
