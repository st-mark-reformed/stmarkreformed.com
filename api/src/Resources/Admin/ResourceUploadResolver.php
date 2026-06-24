<?php

declare(strict_types=1);

namespace App\Resources\Admin;

use App\Resources\Persistence\Persist\ResourceFileStorage;
use App\Resources\ResourceDownload;
use App\Resources\ResourceDownloads;
use App\Uploads\UploadHandle;
use RxAnte\AppBootstrap\Request\ServerRequest;

use function is_array;
use function is_string;

/**
 * Turns the admin create/edit request into a resolved ResourceDownloads
 * collection of on-disk filenames.
 *
 * Each download row is either a new file (an "upload:{id}" handle in `file`,
 * claimed onto disk under the resource's {slug} folder) or an already-stored file
 * (kept by its `filename`). New files are detected by the "upload:" prefix. Files
 * are claimed here, in the admin layer, so the domain entity only ever holds real
 * filenames — the same shape the importer and Redis generator rely on. A failed
 * DB write afterward could orphan a freshly written file under the resource's own
 * {slug} folder; it is harmless and overwritten on the next save.
 */
readonly class ResourceUploadResolver
{
    public function __construct(private ResourceFileStorage $storage)
    {
    }

    public function resolveDownloads(
        string $slug,
        ServerRequest $request,
    ): ResourceDownloads {
        $raw = $request->parsedBody->attributes['downloads'] ?? null;

        if (! is_array($raw)) {
            return new ResourceDownloads();
        }

        $downloads = [];

        foreach ($raw as $rawDownload) {
            if (! is_array($rawDownload)) {
                continue;
            }

            $download = $this->resolveDownload(
                slug: $slug,
                rawDownload: $rawDownload,
            );

            if ($download === null) {
                continue;
            }

            $downloads[] = $download;
        }

        return new ResourceDownloads(downloads: $downloads);
    }

    /** @param array<array-key, mixed> $rawDownload */
    private function resolveDownload(
        string $slug,
        array $rawDownload,
    ): ResourceDownload|null {
        $filename = $this->stringValue(value: $rawDownload['filename'] ?? null);
        $file     = $this->stringValue(value: $rawDownload['file'] ?? null);

        if (UploadHandle::isHandle($file)) {
            return new ResourceDownload(
                filename: $this->storage->saveDownload(
                    uploadId: UploadHandle::fromValue($file)->uploadId,
                    slug: $slug,
                    fileName: $filename,
                ),
            );
        }

        if ($filename === '') {
            return null;
        }

        return new ResourceDownload(filename: $filename);
    }

    private function stringValue(mixed $value): string
    {
        return is_string($value) ? $value : '';
    }
}
