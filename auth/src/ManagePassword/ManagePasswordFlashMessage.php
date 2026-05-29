<?php

declare(strict_types=1);

namespace App\ManagePassword;

readonly class ManagePasswordFlashMessage
{
    public function __construct(
        public string $title,
        public MessageType $type = MessageType::error,
        public string $body = '',
    ) {
    }

    /** @return array<string, string> */
    public function asArray(): array
    {
        return [
            'title' => $this->title,
            'type' => $this->type->value,
            'body' => $this->body,
        ];
    }
}
