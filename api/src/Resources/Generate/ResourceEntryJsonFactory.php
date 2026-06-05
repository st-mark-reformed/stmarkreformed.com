<?php

declare(strict_types=1);

namespace App\Resources\Generate;

use App\Resources\ResourceItem;

readonly class ResourceEntryJsonFactory
{
    /**
     * Emits the exact entry shape the public front-end already consumes
     * (see web/app/resources/repository/ResourceItem.ts): the download URL is
     * derived front-end-side from slug + filename, so only the filename ships.
     *
     * @return array{
     *     title: string,
     *     slug: string,
     *     body: string,
     *     resourceDownloads: array<array-key, array{filename: string}>,
     * }
     */
    public function create(ResourceItem $resourceItem): array
    {
        return [
            'title' => $resourceItem->title,
            'slug' => $resourceItem->slug,
            'body' => $resourceItem->body,
            'resourceDownloads' => $resourceItem->downloads->asArray(),
        ];
    }
}
