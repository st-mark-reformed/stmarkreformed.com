<?php

declare(strict_types=1);

namespace App\Profiles\Profile;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Spatie\Cloneable\Cloneable;

use function array_merge;
use function trim;

readonly class Profile
{
    use Cloneable;

    public bool $isValid;

    /** @var string[] */
    public array $errorMessages;

    public UuidInterface $id;

    public string $fullName;

    public string $fullNameWithPosition;

    public string $fullNameWithHonorific;

    public string $fullNameWithHonorificAndPosition;

    public function __construct(
        public Slug $slug,
        public FirstName $firstName,
        public LastName $lastName,
        public string $titleOrHonorific,
        public Email $email,
        public LeadershipPosition $leadershipPosition,
        UuidInterface|null $id = null,
    ) {
        if ($id === null) {
            $this->id = Uuid::uuid6();
        } else {
            $this->id = $id;
        }

        $isValid = true;

        $errorMessages = [];

        if (! $slug->isValid) {
            $isValid         = false;
            $errorMessages[] = $slug->errorMessage;
        }

        if (! $firstName->isValid) {
            $isValid         = false;
            $errorMessages[] = $firstName->errorMessage;
        }

        if (! $lastName->isValid) {
            $isValid         = false;
            $errorMessages[] = $lastName->errorMessage;
        }

        if (! $email->isValid) {
            $isValid         = false;
            $errorMessages[] = $email->errorMessage;
        }

        if ($leadershipPosition === LeadershipPosition::INVALID) {
            $isValid         = false;
            $errorMessages[] = 'Leadership Position must be a valid value';
        }

        $this->isValid = $isValid;

        $this->errorMessages = $errorMessages;

        // Set fullName
        $this->fullName = trim(
            $firstName->firstName . ' ' . $lastName->lastName,
        );

        // Set fullNameWithPosition
        $fullNameWithPosition = $this->fullName;

        if ($leadershipPosition->humanReadable() !== '') {
            $fullNameWithPosition .= ' (' . $leadershipPosition->humanReadable() . ')';
        }

        $this->fullNameWithPosition = $fullNameWithPosition;

        // Set fullNameWithHonorific
        $this->fullNameWithHonorific = trim(
            $titleOrHonorific . ' ' . $this->fullName,
        );

        // Set fullNameWithHonorificAndPosition
        $this->fullNameWithHonorificAndPosition = trim(
            $titleOrHonorific . ' ' . $fullNameWithPosition,
        );
    }

    /** @return scalar[] */
    public function asScalar(): array
    {
        return [
            'id' => $this->id->toString(),
            'slug' => $this->slug->slug,
            'firstName' => $this->firstName->firstName,
            'lastName' => $this->lastName->lastName,
            'titleOrHonorific' => $this->titleOrHonorific,
            'email' => $this->email->address,
            'leadershipPosition' => $this->leadershipPosition->humanReadable(),
            'fullName' => $this->fullName,
            'fullNameWithPosition' => $this->fullNameWithPosition,
            'fullNameWithHonorific' => $this->fullNameWithHonorific,
            'fullNameWithHonorificAndPosition' => $this->fullNameWithHonorificAndPosition,
        ];
    }

    public function withId(UuidInterface $id): Profile
    {
        return $this->with(id: $id);
    }

    public function withIdFromString(string $id): Profile
    {
        return $this->withId(Uuid::fromString($id));
    }

    public function withErrorMessage(string $message): Profile
    {
        return $this->with(isValid: false)->with(errorMessages: array_merge(
            $this->errorMessages,
            [$message],
        ));
    }
}
