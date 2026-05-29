<?php

declare(strict_types=1);

namespace App\ManagePassword;

use function array_map;
use function mb_strlen;

readonly class PostData
{
    public bool $isValid;

    /** @var string[] */
    public array $validationMessages;

    public function __construct(
        public string $currentPassword,
        public string $newPassword,
        public string $confirmPassword,
    ) {
        $isValid = true;

        $validationMessages = [];

        if (mb_strlen($newPassword) < 8) {
            $validationMessages[] = 'Your new password must be at least 8 characters long.';

            $isValid = false;
        }

        if ($newPassword !== $confirmPassword) {
            $validationMessages[] = 'The new password and confirmation do not match.';

            $isValid = false;
        }

        if ($currentPassword === '') {
            $validationMessages[] = 'Your current password is required.';

            $isValid = false;
        }

        $this->isValid = $isValid;

        $this->validationMessages = $validationMessages;
    }

    public function walkValidationMessages(callable $callback): void
    {
        array_map($callback, $this->validationMessages);
    }
}
