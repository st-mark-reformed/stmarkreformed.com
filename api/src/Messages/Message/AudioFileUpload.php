<?php

declare(strict_types=1);

namespace App\Messages\Message;

readonly class AudioFileUpload
{
    public function __construct(
        public string $name = '',
        public string $data = '',
    ) {
    }

    public function hasData(): bool
    {
        return $this->data !== '';
    }
}
