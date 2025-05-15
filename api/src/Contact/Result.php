<?php

declare(strict_types=1);

namespace App\Contact;

use function array_map;

readonly class Result
{
    /** @param string[] $errors */
    public function __construct(
        public bool $success,
        public string $message,
        public array $errors,
    ) {
        array_map(static fn (string $error) => $error, $errors);
    }

    /** @return array<scalar | array<scalar>> */
    public function asScalarArray(): array
    {
        return [
            'success' => $this->success,
            'message' => $this->message,
            'errors' => $this->errors,
        ];
    }
}
