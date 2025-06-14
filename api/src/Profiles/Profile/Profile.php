<?php

declare(strict_types=1);

namespace App\Profiles\Profile;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Spatie\Cloneable\Cloneable;

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
}
