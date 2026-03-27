<?php

declare(strict_types=1);

namespace App\Profiles;

use function count;

readonly class NewProfile
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public string $titleOrHonorific = '',
        public string $firstName = '',
        public string $lastName = '',
        public ProfileEmail $email = new ProfileEmail(),
        public ProfileLeadershipPosition $leadershipPosition = ProfileLeadershipPosition::none,
        public string $bio = '',
        public bool $hasMessages = false,
    ) {
        $messages = ProfileValidation::validate($this);

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
