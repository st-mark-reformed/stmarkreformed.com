<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\Shared\AudioPlayer;

class AudioPlayerKeyValItem
{
    public function __construct(
        private string $key,
        private string $value,
        private string $href = '',
    ) {
    }

    public function key(): string
    {
        return $this->key;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function href(): string
    {
        return $this->href;
    }
}
