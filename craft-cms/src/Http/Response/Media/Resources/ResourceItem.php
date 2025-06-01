<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resources;

use App\Http\Response\Media\Resource\ResourceDownloadItem;
use Twig\Markup;

class ResourceItem
{
    /**
     * @param ResourceDownloadItem[] $resourceDownloads
     */
    public function __construct(
        public string $title,
        public string $url,
        public string $slug,
        public Markup $body,
        public array $resourceDownloads,
    ) {
        array_map(
            static fn (ResourceDownloadItem $i) => $i,
            $resourceDownloads,
        );
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
