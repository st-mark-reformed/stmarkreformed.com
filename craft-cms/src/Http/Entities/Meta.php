<?php

declare(strict_types=1);

namespace App\Http\Entities;

// phpcs:disable Generic.Files.LineLength.TooLong

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
     * @return array<array-key, string|array<array-key, string>>
     */
    public function preConnect(): array
    {
        return [
            'https://fonts.googleapis.com',
            [
                'href'  => 'https://fonts.gstatic.com',
                'attributes' => 'crossorigin',
            ],
        ];
    }

    /**
     * @return string[]
     */
    public function stylesheets(): array
    {
        return ['https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,800;1,300;1,400;1,800&display=swap'];
    }

    /**
     * @return string[]
     */
    public function jsFiles(): array
    {
        return ['https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.x.x/dist/alpine.min.js'];
    }
}
