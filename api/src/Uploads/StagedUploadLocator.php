<?php

declare(strict_types=1);

namespace App\Uploads;

/**
 * Read-only seam the feature storage classes consume so they never reference
 * the upload staging base path directly.
 */
readonly class StagedUploadLocator
{
    public function __construct(private UploadChunkStore $chunkStore)
    {
    }

    public function sessionFor(string $uploadId): UploadSession|null
    {
        return $this->chunkStore->readSession(uploadId: $uploadId);
    }

    public function assembledPathFor(string $uploadId): string
    {
        return $this->chunkStore->assembledPath(uploadId: $uploadId);
    }

    public function discard(string $uploadId): void
    {
        $this->chunkStore->deleteSession(uploadId: $uploadId);
    }
}
