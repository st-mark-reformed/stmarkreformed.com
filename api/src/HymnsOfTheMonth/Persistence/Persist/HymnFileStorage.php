<?php

declare(strict_types=1);

namespace App\HymnsOfTheMonth\Persistence\Persist;

use RuntimeException;

use function base64_decode;
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
use function strtolower;
use function substr;
use function unlink;

/**
 * Writes hymn uploads into the shared filesAboveWebroot volume, preserving the
 * Craft layout ({slug}/music and {slug}/audio) so migrated and newly uploaded
 * files share one convention and the existing member download route resolves
 * both.
 *
 * Inputs are base64 data URIs (data:<mime>;base64,<payload>) produced by the
 * admin uploader. File names are derived here rather than trusted from the
 * client: the sheet is "sheet.{ext}" and each practice track is named from its
 * (slugified) title. Practice tracks must be MP3; the sheet may be any type
 * (matching Craft, where the music field allowed all types).
 */
readonly class HymnFileStorage
{
    private const string BASE_PATH = '/var/www/filesAboveWebroot';

    private const string MUSIC_DIR = 'music';

    private const string AUDIO_DIR = 'audio';

    /** @return string the stored relative path, e.g. "january-2024/music/sheet.pdf" */
    public function saveSheet(string $dataUri, string $slug): string
    {
        [$decoded, $mime] = $this->decode(dataUri: $dataUri);

        return $this->write(
            slug: $slug,
            subDir: self::MUSIC_DIR,
            fileName: 'sheet.' . $this->extensionForMime(mime: $mime),
            decoded: $decoded,
        );
    }

    /** @return string the stored relative path, e.g. "january-2024/audio/full-mix.mp3" */
    public function saveTrack(
        string $dataUri,
        string $slug,
        string $fileNameBase,
    ): string {
        [$decoded] = $this->decode(dataUri: $dataUri);

        if (! $this->isMp3(decoded: $decoded)) {
            throw new RuntimeException('Practice track is not a valid MP3.');
        }

        return $this->write(
            slug: $slug,
            subDir: self::AUDIO_DIR,
            fileName: $fileNameBase . '.mp3',
            decoded: $decoded,
        );
    }

    public function deleteAllForSlug(string $slug): void
    {
        if ($slug === '') {
            return;
        }

        $this->deleteRecursive(path: sprintf('%s/%s', self::BASE_PATH, $slug));
    }

    /** @return array{0: string, 1: string} the decoded bytes and the mime type */
    private function decode(string $dataUri): array
    {
        $mime    = '';
        $payload = $dataUri;

        if (str_starts_with($dataUri, 'data:')) {
            $commaPosition = strpos($dataUri, ',');

            if ($commaPosition === false) {
                throw new RuntimeException('Invalid data URI.');
            }

            $header = substr($dataUri, 5, $commaPosition - 5);

            $semicolonPosition = strpos($header, ';');

            $mime = $semicolonPosition === false
                ? $header
                : substr($header, 0, $semicolonPosition);

            $payload = substr($dataUri, $commaPosition + 1);
        }

        $decoded = base64_decode($payload, true);

        if ($decoded === false || $decoded === '') {
            throw new RuntimeException('Invalid base64 file payload.');
        }

        return [$decoded, $mime];
    }

    private function extensionForMime(string $mime): string
    {
        $slashPosition = strpos($mime, '/');

        if ($slashPosition === false) {
            return 'pdf';
        }

        $extension = preg_replace(
            '/[^A-Za-z0-9]+/',
            '',
            substr($mime, $slashPosition + 1),
        ) ?? '';

        return $extension === '' ? 'pdf' : strtolower($extension);
    }

    private function write(
        string $slug,
        string $subDir,
        string $fileName,
        string $decoded,
    ): string {
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
                sprintf('%s/%s', $directory, $fileName),
                $decoded,
            ) === false
        ) {
            throw new RuntimeException('Unable to write uploaded file.');
        }

        return sprintf('%s/%s/%s', $slug, $subDir, $fileName);
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
