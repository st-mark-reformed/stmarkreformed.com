<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Messages\SearchForm;

class SelectOption
{
    public function __construct(
        private string $name,
        private string $slug,
        private bool $isActive = false,
    ) {
    }

    public function name(): string
    {
        return $this->name;
    }

    public function slug(): string
    {
        return $this->slug;
    }

    public function isActive(): bool
    {
        return $this->isActive;
    }
}
