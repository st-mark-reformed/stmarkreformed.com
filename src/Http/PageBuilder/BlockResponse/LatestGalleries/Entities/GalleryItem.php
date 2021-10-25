<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestGalleries\Entities;

class GalleryItem
{
    public function __construct(
        private string $keyImageUrl,
        private string $title,
        private string $url,
    ) {
    }

    public function keyImageUrl(): string
    {
        return $this->keyImageUrl;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function url(): string
    {
        return $this->url;
    }
}
