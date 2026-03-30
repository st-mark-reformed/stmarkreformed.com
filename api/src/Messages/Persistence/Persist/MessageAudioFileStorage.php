<?php

declare(strict_types=1);

namespace App\Messages\Persistence\Persist;

use RuntimeException;

use function base64_decode;
use function dirname;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function ord;
use function str_starts_with;
use function strlen;
use function strpos;
use function substr;

readonly class MessageAudioFileStorage
{
    public function save(string $base64Audio, string $absoluteFilePath): void
    {
        $payload = $this->extractBase64Payload($base64Audio);

        $decoded = base64_decode($payload, true);

        if ($decoded === false || $decoded === '') {
            throw new RuntimeException(
                'Invalid base64 audio payload.',
            );
        }

        if (! $this->isMp3($decoded)) {
            throw new RuntimeException(
                'Audio payload is not a valid MP3.',
            );
        }

        $directory = dirname($absoluteFilePath);

        if (
            ! is_dir($directory) &&
            ! mkdir($directory, 0775, true) &&
            ! is_dir($directory)
        ) {
            throw new RuntimeException(
                'Unable to create audio directory.',
            );
        }

        if (
            file_put_contents(
                $absoluteFilePath,
                $decoded,
            ) === false
        ) {
            throw new RuntimeException('Unable to write audio file.');
        }
    }

    private function extractBase64Payload(string $value): string
    {
        if (! str_starts_with($value, 'data:')) {
            return $value;
        }

        $commaPosition = strpos($value, ',');

        if ($commaPosition === false) {
            throw new RuntimeException('Invalid data URI.');
        }

        return substr($value, $commaPosition + 1);
    }

    private function isMp3(string $decoded): bool
    {
        if (str_starts_with($decoded, 'ID3')) {
            return true;
        }

        if (strlen($decoded) < 2) {
            return false;
        }

        return ord($decoded[0]) === 0xFF
            && (ord($decoded[1]) & 0xE0) === 0xE0;
    }
}
