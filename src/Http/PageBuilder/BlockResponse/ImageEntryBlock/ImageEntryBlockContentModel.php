<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\ImageEntryBlock;

class ImageEntryBlockContentModel
{
    public function __construct(
        private string $imageTitle,
        private string $imageUrl,
    ) {
    }

    public function imageTitle(): string
    {
        return $this->imageTitle;
    }

    public function imageUrl(): string
    {
        return $this->imageUrl;
    }
}
