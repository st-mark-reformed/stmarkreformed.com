<?php

declare(strict_types=1);

namespace App\Uploads\Purge;

use App\Uploads\UploadChunkStore;
use Psr\Clock\ClockInterface;

readonly class PurgeAbandonedUploads
{
    public const string JOB_HANDLE = 'purge-abandoned-uploads';

    public const string JOB_NAME = 'Purge Abandoned Uploads';

    private const int MAX_AGE_SECONDS = 86400;

    public function __construct(
        private UploadChunkStore $chunkStore,
        private ClockInterface $clock,
    ) {
    }

    public function purge(): void
    {
        $now = $this->clock->now()->getTimestamp();

        foreach ($this->chunkStore->listUploadIds() as $uploadId) {
            $session = $this->chunkStore->readSession(uploadId: $uploadId);

            // A directory with no readable manifest is unrecoverable; remove it.
            if ($session === null) {
                $this->chunkStore->deleteSession(uploadId: $uploadId);

                continue;
            }

            if ($now - $session->createdAt <= self::MAX_AGE_SECONDS) {
                continue;
            }

            $this->chunkStore->deleteSession(uploadId: $uploadId);
        }
    }
}
