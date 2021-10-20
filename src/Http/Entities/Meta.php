<?php

declare(strict_types=1);

namespace App\Http\Entities;

class Meta
{
    public function __construct(
        private string $metaTitle = '',
    ) {
    }

    public function metaTitle(): string
    {
        return $this->metaTitle;
    }

    public function siteName(): string
    {
        return 'St. Mark Reformed Church';
    }

    /**
     * @return string[]
     */
    public function stylesheets(): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function jsFiles(): array
    {
        return [];
    }
}
