<?php

declare(strict_types=1);

namespace App\LogIn;

readonly class LogInFlashErrorMessage
{
    public function __construct(
        public string $title,
        public string $body = '',
    ) {
    }

    /** @return array<string, string> */
    public function asArray(): array
    {
        return [
            'title' => $this->title,
            'body' => $this->body,
        ];
    }
}
