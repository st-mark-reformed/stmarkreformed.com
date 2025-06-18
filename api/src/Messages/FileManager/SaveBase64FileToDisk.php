<?php

declare(strict_types=1);

namespace App\Messages\FileManager;

use RuntimeException;
use Symfony\Component\Filesystem\Filesystem;

use function base64_decode;
use function count;
use function explode;
use function is_string;

readonly class SaveBase64FileToDisk
{
    public function __construct(private Filesystem $filesystem)
    {
    }

    public function save(
        string $filePath,
        string $base64Data,
    ): void {
        $exception = new RuntimeException(
            'Malformed base64 audio data',
        );

        $parts = explode(',', $base64Data);

        if (count($parts) !== 2) {
            throw $exception;
        }

        $leader = $parts[0];
        $data   = $parts[1];

        if ($leader !== 'data:audio/mpeg;base64') {
            throw $exception;
        }

        $binaryData = base64_decode($data, true);

        if (! is_string($binaryData)) {
            throw $exception;
        }

        $this->filesystem->dumpFile($filePath, $binaryData);
    }
}
