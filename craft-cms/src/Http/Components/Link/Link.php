<?php

declare(strict_types=1);

namespace App\Http\Components\Link;

class Link
{
    public function __construct(
        private bool $isEmpty,
        private string $content = '',
        private string $href = '',
        private bool $newWindow = false,
    ) {
    }

    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }

    public function content(): string
    {
        return $this->content;
    }

    public function href(): string
    {
        return $this->href;
    }

    public function newWindow(): bool
    {
        return $this->newWindow;
    }
}
