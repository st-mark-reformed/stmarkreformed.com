<?php

declare(strict_types=1);

namespace App\Uploads\Http;

use JsonSerializable;

readonly class UploadProgressResponse implements JsonSerializable
{
    /** @param int[] $receivedChunks */
    public function __construct(
        private string $uploadId,
        private array $receivedChunks,
        private int $totalChunks,
    ) {
    }

    /**
     * @return array{
     *     uploadId: string,
     *     receivedChunks: int[],
     *     totalChunks: int,
     * }
     */
    public function jsonSerialize(): array
    {
        return [
            'uploadId' => $this->uploadId,
            'receivedChunks' => $this->receivedChunks,
            'totalChunks' => $this->totalChunks,
        ];
    }
}
