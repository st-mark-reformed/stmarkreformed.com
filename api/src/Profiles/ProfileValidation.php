<?php

declare(strict_types=1);

namespace App\Profiles;

readonly class ProfileValidation
{
    /** @return string[] */
    public static function validate(NewProfile|Profile $profile): array
    {
        $messages = [];

        if ($profile->firstName === '') {
            $messages['firstName'] = 'A first name is required.';
        }

        if ($profile->lastName === '') {
            $messages['lastName'] = 'A last name is required.';
        }

        if (! $profile->email->isValid) {
            $messages['email'] = 'If an email address is provided, it must be valid.';
        }

        return $messages;
    }
}
