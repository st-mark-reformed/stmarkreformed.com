<?php

declare(strict_types=1);

namespace App\Http\PageBuilder\BlockResponse\LatestGalleries\Entities;

use function array_map;

class LatestGalleriesContentModel
{
    /** @var GalleryItem[] */
    private array $galleryItems;

    /**
     * @param GalleryItem[] $galleryItems
     */
    public function __construct(
        private string $heading,
        private string $subHeading,
        array $galleryItems,
    ) {
        array_map(
            [$this, 'addGalleryItem'],
            $galleryItems,
        );
    }

    private function addGalleryItem(GalleryItem $galleryItem): void
    {
        $this->galleryItems[] = $galleryItem;
    }

    public function heading(): string
    {
        return $this->heading;
    }

    public function subHeading(): string
    {
        return $this->subHeading;
    }

    public function hasHeadings(): bool
    {
        return $this->heading !== '' || $this->subHeading !== '';
    }

    /**
     * @return GalleryItem[]
     */
    public function galleryItems(): array
    {
        return $this->galleryItems;
    }
}
