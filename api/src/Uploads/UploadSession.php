<?php

declare(strict_types=1);

namespace App\Uploads;

use function is_numeric;
use function is_string;

readonly class UploadSession
{
    public function __construct(
        public string $uploadId,
        public string $fileName,
        public int $totalSize,
        public int $chunkSize,
        public int $totalChunks,
        public UploadAcceptType $acceptType,
        public int $createdAt,
    ) {
    }

    /**
     * @return array{
     *     uploadId: string,
     *     fileName: string,
     *     totalSize: int,
     *     chunkSize: int,
     *     totalChunks: int,
     *     acceptType: string,
     *     createdAt: int,
     * }
     */
    public function toManifestArray(): array
    {
        return [
            'uploadId' => $this->uploadId,
            'fileName' => $this->fileName,
            'totalSize' => $this->totalSize,
            'chunkSize' => $this->chunkSize,
            'totalChunks' => $this->totalChunks,
            'acceptType' => $this->acceptType->value,
            'createdAt' => $this->createdAt,
        ];
    }

    /** @param array<array-key, mixed> $data */
    public static function fromManifestArray(array $data): self
    {
        return new self(
            uploadId: self::readString($data, 'uploadId'),
            fileName: self::readString($data, 'fileName'),
            totalSize: self::readInt($data, 'totalSize'),
            chunkSize: self::readInt($data, 'chunkSize'),
            totalChunks: self::readInt($data, 'totalChunks'),
            acceptType: UploadAcceptType::fromString(
                self::readString($data, 'acceptType'),
            ),
            createdAt: self::readInt($data, 'createdAt'),
        );
    }

    /** @param array<array-key, mixed> $data */
    private static function readString(array $data, string $key): string
    {
        $value = $data[$key] ?? null;

        return is_string($value) ? $value : '';
    }

    /** @param array<array-key, mixed> $data */
    private static function readInt(array $data, string $key): int
    {
        $value = $data[$key] ?? null;

        return is_numeric($value) ? (int) $value : 0;
    }
}
