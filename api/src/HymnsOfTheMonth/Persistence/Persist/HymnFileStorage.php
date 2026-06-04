<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Persist;

use RuntimeException;

use function base64_decode;
use function basename;
use function file_exists;
use function file_put_contents;
use function is_dir;
use function mkdir;
use function ord;
use function preg_replace;
use function rmdir;
use function scandir;
use function sprintf;
use function str_starts_with;
use function strlen;
use function strpos;
use function substr;
use function trim;
use function unlink;

/**
 * Writes hymn uploads into the shared filesAboveWebroot volume, preserving the
 * Craft layout ({slug}/music and {slug}/audio) so migrated and newly uploaded
 * files share one convention and the existing member download route resolves
 * both. Practice tracks must be MP3; the sheet may be any file type (matching
 * Craft, where the music field allowed all types).
 */
readonly class HymnFileStorage
{
    private const string BASE_PATH = '/var/www/filesAboveWebroot';

    private const string MUSIC_DIR = 'music';

    private const string AUDIO_DIR = 'audio';

    /** @return string the stored relative path, e.g. "january-2024/music/sheet.pdf" */
    public function saveSheet(string $base64, string $slug, string $fileName): string
    {
        return $this->save(
            base64: $base64,
            slug: $slug,
            subDir: self::MUSIC_DIR,
            fileName: $fileName,
            requireMp3: false,
        );
    }

    /** @return string the stored relative path, e.g. "january-2024/audio/full-mix.mp3" */
    public function saveTrack(string $base64, string $slug, string $fileName): string
    {
        return $this->save(
            base64: $base64,
            slug: $slug,
            subDir: self::AUDIO_DIR,
            fileName: $fileName,
            requireMp3: true,
        );
    }

    public function deleteAllForSlug(string $slug): void
    {
        if ($slug === '') {
            return;
        }

        $this->deleteRecursive(path: sprintf('%s/%s', self::BASE_PATH, $slug));
    }

    private function save(
        string $base64,
        string $slug,
        string $subDir,
        string $fileName,
        bool $requireMp3,
    ): string {
        $decoded = base64_decode($this->extractBase64Payload($base64), true);

        if ($decoded === false || $decoded === '') {
            throw new RuntimeException('Invalid base64 file payload.');
        }

        if ($requireMp3 && ! $this->isMp3($decoded)) {
            throw new RuntimeException('Practice track is not a valid MP3.');
        }

        $safeName = $this->sanitizeFileName($fileName);

        if ($safeName === '') {
            throw new RuntimeException('A file name is required for uploads.');
        }

        $directory = sprintf('%s/%s/%s', self::BASE_PATH, $slug, $subDir);

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

        return sprintf('%s/%s/%s', $slug, $subDir, $safeName);
    }

    private function sanitizeFileName(string $fileName): string
    {
        $name = preg_replace(
            '/[^A-Za-z0-9._-]+/',
            '-',
            basename($fileName),
        ) ?? '';

        return trim($name, '-');
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
