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
        $messages = [];

        if ($this->firstName === '') {
            $messages['firstName'] = 'A first name is required.';
        }

        if ($this->lastName === '') {
            $messages['lastName'] = 'A last name is required.';
        }

        if (! $this->email->isValid) {
            $messages['email'] = 'If an email address is provided, it must be valid.';
        }

        $this->isValid = count($messages) < 1;

        $this->validationMessages = $messages;
    }
}
