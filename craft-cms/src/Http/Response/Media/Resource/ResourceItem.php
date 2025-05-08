<?php

declare(strict_types=1);

namespace App\Http\Response\Media\Resource;

use Twig\Markup;

use function array_map;

class ResourceItem
{
    /** @var ResourceDownloadItem[] */
    private array $resourceDownloads = [];

    /**
     * @param ResourceDownloadItem[] $resourceDownloads
     */
    public function __construct(
        private string $title,
        private Markup $body,
        array $resourceDownloads,
    ) {
        array_map(
            function (ResourceDownloadItem $resourceDownload): void {
                $this->resourceDownloads[] = $resourceDownload;
            },
            $resourceDownloads,
        );
    }

    public function title(): string
    {
        return $this->title;
    }

    public function body(): Markup
    {
        return $this->body;
    }

    public function hasBody(): bool
    {
        return (string) $this->body() !== '';
    }

    /**
     * @return ResourceDownloadItem[]
     */
    public function resourceDownloads(): array
    {
        return $this->resourceDownloads;
    }

    /**
     * @return mixed[]
     */
    public function map(callable $callable): array
    {
        return array_map(
            $callable,
            $this->resourceDownloads(),
        );
    }
}
