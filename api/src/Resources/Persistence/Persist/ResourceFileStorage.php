<?php

declare(strict_types=1);

namespace App\Resources\Persistence\Persist;

use RuntimeException;

use function base64_decode;
use function basename;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function preg_replace;
use function rmdir;
use function scandir;
use function sprintf;
use function str_starts_with;
use function strpos;
use function substr;
use function trim;
use function unlink;

/**
 * Writes resource download files into the shared public uploads volume,
 * preserving the Craft layout (general/resources/{slug}/{filename}) so migrated
 * and newly uploaded files share one convention and the existing public URL
 * (/uploads/general/resources/{slug}/{filename}) keeps resolving.
 *
 * Inputs are base64 data URIs (data:<mime>;base64,<payload>) produced by the
 * admin uploader. Unlike the hymn storage (which derives names), the original
 * filename is preserved here — sanitized — because it is user-visible and forms
 * the public download URL. All file types are allowed (matching Craft, where the
 * resource download field allowed all kinds).
 */
readonly class ResourceFileStorage
{
    private const string BASE_PATH = '/var/www/public/uploads/general/resources';

    /** @return string the stored (sanitized) filename, e.g. "smrc-liturgy.pdf" */
    public function saveDownload(
        string $dataUri,
        string $slug,
        string $fileName,
    ): string {
        $decoded  = $this->decode(dataUri: $dataUri);
        $safeName = $this->sanitizeFileName(fileName: $fileName);

        $directory = sprintf('%s/%s', self::BASE_PATH, $slug);

        if (
            ! is_dir($directory) &&
            ! mkdir($directory, 0775, true) &&
            ! is_dir($directory)
        ) {
            throw new RuntimeException('Unable to create upload directory.');
        }

        if (
            file_put_contents(
                sprintf('%s/%s', $directory, $safeName),
                $decoded,
            ) === false
        ) {
            throw new RuntimeException('Unable to write uploaded file.');
        }

        return $safeName;
    }

    public function deleteAllForSlug(string $slug): void
    {
        if ($slug === '') {
            return;
        }

        $this->deleteRecursive(path: sprintf('%s/%s', self::BASE_PATH, $slug));
    }

    private function decode(string $dataUri): string
    {
        $payload = $dataUri;

        if (str_starts_with($dataUri, 'data:')) {
            $commaPosition = strpos($dataUri, ',');

            if ($commaPosition === false) {
                throw new RuntimeException('Invalid data URI.');
            }

            $payload = substr($dataUri, $commaPosition + 1);
        }

        $decoded = base64_decode($payload, true);

        if ($decoded === false || $decoded === '') {
            throw new RuntimeException('Invalid base64 file payload.');
        }

        return $decoded;
    }

    private function sanitizeFileName(string $fileName): string
    {
        // basename drops any directory components; then strip path separators
        // and control characters while keeping the name human-readable.
        $name = preg_replace(
            '/[\x00-\x1F\/\\\\]+/',
            '',
            basename($fileName),
        ) ?? '';

        $name = trim($name);

        if ($name === '' || $name === '.' || $name === '..') {
            return 'download';
        }

        return $name;
    }

    private function deleteRecursive(string $path): void
    {
        if (! file_exists($path)) {
            return;
        }

        if (! is_dir($path)) {
            unlink($path);

            return;
        }

        $entries = scandir($path);

        foreach ($entries === false ? [] : $entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }

            $this->deleteRecursive(path: sprintf('%s/%s', $path, $entry));
        }

        rmdir($path);
    }
}
